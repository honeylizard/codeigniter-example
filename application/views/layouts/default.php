<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
	<head>
		<title><?php echo $meta_title; ?></title>
		<meta name="description" content="<?php echo $meta_description; ?>">
		<meta name="keywords" content="<?php echo $meta_keywords; ?>">

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta name="author" content="Honey Lizard">
		<meta name="copyright" content="2017 &copy; Honey Lizard">
		<meta http-equiv="content-language" content="<?php echo $language; ?>">

		<link href="<?php echo $bootstrap_css; ?>" rel="stylesheet">
		<link href="<?php echo $app_css; ?>" rel="stylesheet">
	</head>
	<body>
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="<?php echo $application_url; ?>">
						<?php echo $application_name; ?>
					</a>
				</div>
				<div class="collapse navbar-collapse" id="main-navbar-collapse">
					<?php echo $navigation_links; ?>
				</div>
			</div>
		</nav>
        <?php echo $contents; ?>
		<div class="text-muted text-center">
			2017 &copy; Honeylizard
		</div>
		<script src="<?php echo $jquery_js; ?>"></script>
		<script src="<?php echo $bootstrap_js; ?>"></script>
	</body>
</html>
