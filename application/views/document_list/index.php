<?php // Based on a CI-Tutorial: http://ellislab.com/codeigniter/user-guide/tutorial/news_section.html ?>

<h2><?php echo $documentList->getTitle(); ?></h2>
<p>Ersteller: <?php echo $documentList->getCreator(); ?></p>
<p>Administrator: <?php echo $documentList->getAdmin(); ?></p>
<p>Erstellt am: <?php echo $documentList->created(); ?></p>
<p>Zuletzt ge&auml;ndert am: <?php echo $documentList->lastUpdated(); ?></p>
<h3>Dokumente:</h3>

<?php foreach($documentList->getDocuments() as $lDocument): // Will not work, copied from tutorial

// TODO: Generate Document object for every loop iteration?>

<p>
	<a target="_blank" href="<?php $lDocument->getFileName() ?>"><?php echo 
		$lDocument->getAuthors() . " (" .
		$lDocument->getYear() . "), &ldquo;" .
		$lDocument->getTitle() . "&rdquo;, in: " .
		$lDocument->getEditors() . ": <i>" .
		$lDocument->getPublication() . "</i>, " .
		$lDocument->getPublishingHouseAndPlace() . " " .
		$lDocument->getPages() . ". (" .
		"pdf" /* Temporarily hard coded for testing, TODO: replace with substring function on getFileName() */ . ", "
		"3.3 MB" /* TODO: replace with function that calculates file size */ . ")"
		?>
	</a>
</p>

<?php endforeach ?>