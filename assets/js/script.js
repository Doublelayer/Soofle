var timer;

$(document).ready(function(){

	$(".result").on("click", function()
	{
		var id = $(this).attr("data-linkid");
		var url = $(this).attr("href");

		console.log(id);
		console.log(url);
		if(!id)
		{
			alert("data-linkid attribute not found!");
		}
		else
		{
			increasLinkClicks(id, url);	
		}

		return false;
	});

	var grid = $(".imageResults");

	grid.on("layoutComplete", function()
	{
		$(".gridItem img").css("visibility", "visible");
	})

	grid.masonry(
	{
		itemSelector: '.gridItem',
  		columnWidth: 200,
  		gutter: 5,
  		transitionDuration: "0.2s",
  		isInitLayout: false
	});

	$("[data-fancybox]").fancybox(
		{
			caption : function( instance, item ) {
	        var caption = $(this).data('caption') || '';
	        var siteurl = $(this).data('siteurl') || '';

	        if ( item.type === 'image' ) {
	            caption = (caption.length ? caption + '<br /><hr>' : '')
				+ '<a href="' + item.src + '">Bild anzeigen</a><br>'
				+ '<a href="' + siteurl + '">Internetseite anzeigen</a>';
        }

        return caption;
    },
	    afterShow: function(instance, item)
	    {
	    	increasImageClicks(item.src)
	    }

	});
});

function loadImage(src, classname)
{
	var image = $("<img>");

	image.on("load", function()
	{
		$("." + classname + " a").append(image);

		clearTimeout(timer);

		timer = setTimeout(function()
		{
			$(".imageResults").masonry();
		}, 200);
	});

	image.on("error", function()
	{
		$("." + classname).remove();

		$.post("ajax/broken.php", {src : src});
	});

	image.attr("src", src);
}

function increasLinkClicks(linkId, url)
{
	$.post("ajax/updateLinkCount.php", {linkId: linkId})
	.done(function(result)
	{
		if(result != "")
		{
			alert(result);
			return;
		}

		window.location.href = url;
	});
}

function increasImageClicks(imageUrl)
{
	$.post("ajax/updateImageCount.php", {imageUrl: imageUrl})
	.done(function(result)
	{
		if(result != "")
		{
			alert(result);
			return;
		}

	});
}