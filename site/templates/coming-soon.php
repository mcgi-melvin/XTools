<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */
/** @var PageInstance $pageInstance */
/** @var SiteSettings $settings */

include "chunks/init.php";
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Language" content="en" />
	<title><?php echo $page->title ?></title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<meta content='width=device-width' name='viewport' />
	<style>
		body {
			font-family: Arial;
		}
		#wrapper {
			text-align: center;
			width: 100%;
		}
		#holder {
			margin: 100px auto;
		}
		#soonlogo {
			width: 300px;
			margin-bottom: 30px;
		}
		h1 {

		}
	</style>
</head>
<body>
	<div class="uk-section">
		<div class="uk-container">
			<div uk-grid uk-height-match>
				<img id="soonlogo" src="<?php echo $settings->header_logo->url; ?>" alt="<?php echo $pages->get(1)->site_master_title; ?> logo" />
				<div><?= $page->body_content ?></div>
			</div>
		</div>
	</div>
</body>
</html>
