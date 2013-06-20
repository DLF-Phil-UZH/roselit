<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php echo doctype('html5') ?>
<html>
    <head>
        <title><?php echo $title; ?></title>
		<?php
		echo meta('Content-type', 'text/html; charset=utf-8', 'equiv');
		echo meta('description', 'RoSeLit');
		echo link_tag(base_url('/assets/css/roselit-common.css'));
		?>
   	</head>
    <body>
    	<div class="roselit-navigation">
			<ul>
				<li><a href="<?php echo site_url('manager/documents'); ?>" target="_self">Dokumente</a></li>
				<li><a href="<?php echo site_url('manager/lists'); ?>" target="_self">Listen</a></li>
			</ul>
			<span><a href="<?php echo site_url('auth/logout'); ?>" target="_self" >Abmelden</a></span>
		</div>
		<h1><?php echo $title ?></h1>

<!-- End of file header.php -->
<!-- Location: ./application/views/header.php -->
