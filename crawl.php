<?php 

	include("config.php");
	include("classes/DomDocumentParser.php");

	$allreadyCrawled = array();
	$crawling = array();
	$alreadyFoundImages = array();

	function insertLink($url, $title, $description, $keywords)
	{
		global $con;

		$query = $con -> prepare("INSERT INTO sites(url, title, description, keywords)
								  VALUES(:url, :title, :description, :keywords)");


		$query -> bindParam(":url", $url);
		$query -> bindParam(":title", $title);
		$query -> bindParam(":description", $description);
		$query -> bindParam(":keywords", $keywords);

		return $query -> execute();
	}

	function insertImage($url, $src, $alt, $title)
	{
		global $con;

		$query = $con -> prepare("INSERT INTO images(siteUrl, imageUrl, alt, title)
								  VALUES(:siteUrl, :imageUrl, :alt, :title)");


		$query -> bindParam(":siteUrl", $url);
		$query -> bindParam(":imageUrl", $src);
		$query -> bindParam(":alt", $alt);
		$query -> bindParam(":title", $title);

		$query -> execute();
	}

	function linkExists($url)
	{
		global $con;

		$query = $con -> prepare("SELECT * FROM sites WHERE url = :url");


		$query -> bindParam(":url", $url);
		$query -> execute();

		return  $query -> rowCount() != 0;
	}

	function createLink($src, $url)
	{

		$scheme = parse_url($url)["scheme"];
		$host = parse_url($url)["host"];

		if(substr($src, 0, 2) == "//")
		{
			$src = $scheme . ":" . $src;
		}
		else if(substr($src, 0, 1) == "/")
		{
			$src = $scheme . "://" . $host . $src;
		}
		else if(substr($src, 0, 2) == "./")
		{
			$src = $scheme . "://" . $host . dirname(parse_url($url["path"])) . substring($src, 1);
		}
		else if(substr($src, 0, 3) == "../")
		{
			$src = $scheme . "://" . $host . "/" . $src;
		}
		else if(substr($src, 0, 5) != "https" && substr($src, 0, 4) != "http")
		{
			$src = $scheme . "://" . $host . "/" . $src;
		}

		return $src;
	}

	function getDetails($url)
	{
		global $alreadyFoundImages;

		$parser = new DomDocumentParser($url);
		$titleArray = $parser -> getTitleTags();

		if (sizeof($titleArray) == 0 || $titleArray->item(0) == NULL) {
			return;
		}

		$title = $titleArray -> item(0) -> nodeValue;
		$title = str_replace("\n", "", $title);

		if ($title == "") 
		{
			return;
		}


		$description = "";
		$keywords = "";

		$metasArray = $parser -> getMetaTags();

		foreach ($metasArray as $meta) 
		{
			if ($meta -> getAttribute("name") == "description") {
				$description = $meta -> getAttribute("content");
			}
			if ($meta -> getAttribute("name") == "keywords") {
				$keywords = $meta -> getAttribute("content");
			}
		}

		$description = str_replace("\n", "", $description);
		$keywords = str_replace("\n", "", $keywords);

		if(linkExists($url))
		{
			echo "<br>$url already exists<br>";
		}
		else if (insertLink($url, $title, $description, $keywords)) 
		{
			echo "SUCCES: $url". "<br>";
		}
		else
		{
			echo "ERROR: Failed to insert $url". "<br>";
		}
		
		$imageArray = $parser -> getImages();
		foreach ($imageArray as $image) 
		{
			$src = $image->getAttribute("src");
			$alt = $image->getAttribute("alt");
			$title = $image->getAttribute("title");

			if (!$title && !$alt) {
				continue;
			}

			$src = createLink($src, $url);

			if (!in_array($src, $alreadyFoundImages)) {
				$alreadyFoundImages[] = $src;

				insertImage($url, $src, $alt, $title);
			}
		}
		//echo "URL: $url, Title: $title\n, Descripton: $description, Keywords: $keywords\n";
	}


	function followLinks($url) {

		global $allreadyCrawled;
		global $crawling;

		$parser = new DomDocumentParser($url);

		$linkList = $parser -> getLinks();

		foreach ($linkList as $link) 
		{
			$href = $link -> getAttribute("href");

			if(strpos($href, "#") !== false)
			{
				continue;
			}
			else if(substr($href, 0, 11) == "javascript:")
			{
				continue;
			}

			$href = createLink($href, $url);

			if (!in_array($href, $allreadyCrawled)) 
			{
				$allreadyCrawled[] = $href;
				$crawling[] = $href;

				getDetails($href);
			}
			//else return;

			//echo $href . "<br>";
		}


		array_shift($crawling);

		foreach ($crawling as $site) {
			followLinks($site);
		}
	}

	$startUrl = "https://www.bahn.de/p/view/index.shtml";
	followLinks($startUrl);
 ?>