<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php echo doctype('html5'); ?>
<html>
    <head>
        <title><?php echo $title; ?></title>
		<?php
		echo meta('Content-type', 'text/html; charset=utf-8', 'equiv');
		echo meta('description', 'RoSeLit');
		echo link_tag(base_url('/assets/css/uzh.css')); // UZH standard, lower priority
		echo link_tag(base_url('/assets/css/roselit-common.css')); // Higher priority, customized
		?>
   	</head>
    <body>
		<div class="bodywidth">
			<div id="topline">
				<p>Literaturverwaltung Romanisches Seminar</p>
			</div>
			<div class="floatclear">
			</div>
			<div id="headerarea">
				<div id="uzhlogo">
					<a href="http://www.uzh.ch">
						<img alt="uzh logo" height="80" src="<?php echo base_url('/assets/images/uzh_logo_d_pos_web_main.jpg'); ?>" width="231" />
					</a>
				</div>
				<h1>
					<a href="<?php echo site_url('manager'); ?>">
						<heading><?php echo $title ?></heading>
					</a>
				</h1>
				<!--
				<div class="roselit-navigation">
					<ul>
						<li><a href="<?php // echo site_url('manager/documents'); ?>" target="_self">Dokumente</a></li>
						<li><a href="<?php // echo site_url('manager/lists'); ?>" target="_self">Listen</a></li>
					</ul>
					<span><a href="<?php // echo site_url('auth/logout'); ?>" target="_self" >Abmelden</a></span>
				</div>
				-->
			</div>
			<div class="floatclear">
			</div>
			<div class="endheaderline">
			</div>
			<div id="primarnav">
				<a class="namedanchor" name="primarnav"><!----></a>
				<?php
					if(strcmp($page, 'documents') === 0){ ?>
						<a class="active" href="<?php echo site_url('manager/documents'); ?>">Dokumente</a>
					<?php }
					else{ ?>
						<a href="<?php echo site_url('manager/documents'); ?>">Dokumente</a>
					<?php } ?>
				<div class="linkseparator">&#8226;</div>
				<?php
					if(strcmp($page, 'lists') === 0){ ?>
						<a class="active" href="<?php echo site_url('manager/lists'); ?>">Listen</a>
					<?php }
					else{ ?>
						<a href="<?php echo site_url('manager/lists'); ?>">Listen</a>
					<?php } ?>
			</div>
			<div class="floatclear">
			</div>

<!-- End of file header.php -->
<!-- Location: ./application/views/header.php -->
