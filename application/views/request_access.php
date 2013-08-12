<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

	<!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />-->
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	<script>
		$(function() {
			$( "a" )
				.button();
			});
	</script>
	<div class="roselit_content">
		<h2>Zugriff verweigert</h2>
		<p>Herzlich willkommen auf RoSeLit.</p>
		<p>RoSeLit ist ein Dienst des Romanischen Seminars 
		zum Verwalten der Literaturlisten für die Proseminare.</p>
		<p>Tut uns leid, aber Sie sind nicht berechtigt, Literaturlisten
		zu verwalten. Sind Sie vom Romanischen Seminar? Dann beantragen Sie 
		jetzt mit nur einem Klick einen Zugang:</p>
		<p><a href="<?php echo site_url('auth/request_access') ?>" target="_self">Zugang beantragen</a>.</p>
		<p>Zugriff auf die Literaturlisten haben Sie direkt über die
		entsprechenden OLAT-Kurse.</p>
	</div>
