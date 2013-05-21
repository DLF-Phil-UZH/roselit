<?php // Based on a CI-Tutorial: http://ellislab.com/codeigniter/user-guide/tutorial/news_section.html ?>

<h2><?php echo $documentList->getTitle(); ?></h2>
<p>Ersteller: <?php echo $documentList->getCreator(); ?></p>
<p>Administrator: <?php echo $documentList->getAdmin(); ?></p>
<p>Erstellt am: <?php
	$lDatum = date_format($documentList->getCreated(), "d.m.Y, H:i:s");
	$lDatum = $lDatum." Uhr";
	echo $lDatum;
?></p>
<p>Zuletzt ge&auml;ndert am: <?php
	$lDatum = date_format($documentList->getLastUpdated(), "d.m.Y, H:i:s");
	$lDatum = $lDatum." Uhr";
	echo $lDatum;
?></p>
<h3>Dokumente:</h3>

<?php foreach($documentList->getDocuments() as $lDocument): ?>

<p>
	<a target="_blank" href="<?php $lDocument->getFileName() ?>"><?php echo 
		$lDocument->getAuthors() . " (" .
		// TODO: add edition to document model (wrong data in database)
		$lDocument->getYear() . "), &ldquo;" .
		$lDocument->getTitle() . "&rdquo;, in: " .
		(strlen($lDocument->getEditors()) ? $lDocument->getEditors() . " <i>" : "<i>") .
		$lDocument->getPublication() . "</i>, " .
		(strlen($lDocument->getPublishingHouseAndPlace()) ? $lDocument->getPublishingHouseAndPlace() . " " : "") .
		$lDocument->getPages() . ". (" .
		substr(strrchr($lDocument->getFileName(), "."), 1) . ", " .
		"3.3 MB" /* TODO: replace with function that calculates file size */ . ")";
		?>
	</a>
</p>

<?php endforeach ?>