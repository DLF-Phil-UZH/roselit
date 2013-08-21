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

