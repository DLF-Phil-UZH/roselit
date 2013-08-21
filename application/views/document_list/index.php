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
		<?php
			echo $lDocument->toFormattedString();
			if(strlen($lDocument->getFileName() > 0)){
				echo "&nbsp;(<a target=\"_self\" href=\"../files/" . $documentList->getId() . "/" . $lDocument->getId() . "\">";
				echo substr(strrchr($lDocument->getFileName(), "."), 1) . ", " . round($lDocument->getFileSize("MB"), 2) . " MB";
				echo "</a>)";
			}
			else{
				echo "&nbsp;(Kein Dokument hinterlegt)";
			}
		?>
	</p>
	
	<?php endforeach ?>
  </body>
</html>
