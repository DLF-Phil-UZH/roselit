<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $documentList->getTitle(); ?></title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-type">
  </head>
  <body>
    <h2><?php echo $documentList->getTitle(); ?></h2>
    <p>Zuletzt ge&auml;ndert am: <?php
		$lDatum = date_format($documentList->getLastUpdated(), "d.m.Y, H:i:s");
		$lDatum = $lDatum." Uhr";
		echo $lDatum;
	?></p>

	<?php foreach($documentList->getDocumentsSorted() as $lDocument): ?>

    <p>
	<?php echo 
		$lDocument->getAuthors() . " (" .
		// TODO: add edition to document model (wrong data in database)
		$lDocument->getYear() . "), &ldquo;" .
		$lDocument->getTitle() . "&rdquo;, in: " .
		(strlen($lDocument->getEditors()) ? $lDocument->getEditors() . " <i>" : "<i>") .
		$lDocument->getPublication() . "</i>, " .
		(strlen($lDocument->getPublishingHouseAndPlace()) ? $lDocument->getPublishingHouseAndPlace() . " " : "") .
		$lDocument->getPages() . ". (" ?><a target="_blank" href="<?php echo "assets/uploads/files/" . $lDocument->getFileName() ?>"><?php
		echo substr(strrchr($lDocument->getFileName(), "."), 1) . ", " .
		"3.3 MB" /* TODO: replace with function that calculates file size */;?></a><?php echo ")";?>
    </p>
	
	<?php endforeach ?>
  </body>
</html>