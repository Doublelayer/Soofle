# Soofle

---

This is a Projekt of clonig the google website.

### Installation

```
git clone https://github.com/Doublelayer/Soofle.git
```

1. After you cloned the repository you have to create a mysql database.
   For this take the dump file in "database/soofle_dump.sql"

2. Create a file named "config.php" in root.
   paste and edit with your credetials:

```
<?php
	error_reporting(0);

	ob_start();

	try
	{
		$con = new PDO("mysql:dbname=soofle;host=your-host", "your-username", "your-password");
		$con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	}
	catch (PDOException $e) {
		echo "Connection failed: " . $e -> getMessage();
	}
 ?>
```

#

3. Edit crawl.php and paste your favorite website on the end of the file.

Ater editing you can call the "crawl.php file" for crawling new data. All interesting data will be automaticly store in database. After this process finished you can "search" for your crawled content.
