<?php 

	include("config.php");	
	include("classes/SearchResultsProvider.php");
	include("classes/ImageResultsProvider.php");

	if(isset($_GET["term"])){
		$term = $_GET["term"];
	}
	else {
		exit("Bitte geben Sie einen gültigen Wert ein.");
	}

	$type = isset($_GET["type"]) ? $_GET["type"] : "sites";
	$page = isset($_GET["page"]) ? $_GET["page"] : 1;
?>
<!DOCTYPE html>
<html>
<head>
	<title>Willkomen zu Soofle</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.2/dist/jquery.fancybox.min.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

	<script
			  src="https://code.jquery.com/jquery-3.3.1.min.js"
			  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
			  crossorigin="anonymous">
	</script>
</head>
<body>
	<div class="wrapper">
		<div class="header">
			<div class="headerContent">
				<div class="logoContainer">
					<a href="index.php">
						<img src="assets/images/soofleLogo.png">
					</a>
				</div>

				<div class="searchContainer">
					<form action="search.php" method="GET">
						<div class="searchBarContainer">
							<input type="hidden" name="type" value="<?php echo $type ?>">
							<input class="searchBox" type="text" name="term" value="<?php echo $term?>">
							<button class="searchButton">
								<img src="assets/images/icons/search.png">
							</button>
						</div>
					</form>
				</div>
			</div>
			
			<div class="tabsContainer">
				<ul class="tabList">
					<li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
						<a href='<?php echo "search.php?term=$term&type=sites"; ?>'>
							Seiten
						</a>
					</li>
					<li class="<?php echo $type == 'images' ? 'active' : '' ?>">
						<a href='<?php echo "search.php?term=$term&type=images"; ?>'>
							Bilder
						</a>
					</li>
				</ul>
			</div>
		</div>

		<div class="mainResultSection">
			<?php 
				if ($type == "sites") {
					$resultProvider = new SearchResultsProvider($con);
					$pageSize = 20;
				}
				else
				{
					$resultProvider = new ImageResultsProvider($con);
					$pageSize = 30;
				}

				$numResults = $resultProvider -> getNumResults($term);

				echo "<p class='resultCount'>$numResults Treffer gefunden</p>";

				echo $resultProvider -> getResultsHtml($page, $pageSize, $term);
			 ?>
		</div>

		<div class="paginationContainer">
			<div class="pageButtons">
				<div class="pageNumberContainer">
					<img src="assets/images/pageStart.png">
				</div>

				<?php 

					$pageToShow =  10;
					$numPages = ceil($numResults / $pageSize);
					$pagesLeft = min($pageToShow, $numPages);

					$currentPage = $page - floor($pageToShow/2);

					if ($currentPage < 1) 
					{
						$currentPage = 1;
					}
					if ($currentPage + $pagesLeft > $numPages + 1) 
					{
						$currentPage = $numPages + 1 - $pagesLeft;
					}

					while ( $pagesLeft != 0 && $currentPage <= $numPages) 
					{
						if ($currentPage == $page) 
						{
							echo "<div class='pageNumberContainer'>
								  	<img src='assets/images/pageSelected.png'>
									<span class='pageNumber'>$currentPage</span>
								  </div>";
						}
						else
						{
							echo "<div class='pageNumberContainer'>
									<a href='search.php?term=$term&type=$type&page=$currentPage'>
								  		<img src='assets/images/page.png'>
										<span class='pageNumber'>$currentPage</span>
									</a>
 								 </div>";
						}
						
					$currentPage++;
					$pagesLeft--;
					}					

				 ?>


				<div class="pageNumberContainer">
					<img src="assets/images/pageEnd.png">
				</div>
			</div>
		</div>

	</div>
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.2/dist/jquery.fancybox.min.js"></script>
	<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
	<script type="text/javascript" src="assets/js/script.js"></script>
</body>
</html>
