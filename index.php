<?php
	/*
 	 * Simple Example
 	 */
	require("view-handler.php");

	// create custom key to identify the view, then provide the path including file extension.
	$view = new ViewHandler("HELLO_WORLD","helloworld.php");

	// add view arguments that you defined in the template, such as YOUR_NAME and VIEW_NAME
	// note - you don't need to add the hashes (#) here.
	$view->addViewArgument("YOUR_NAME","Christopher Bartholomew");
	$view->addViewArgument("VIEW_NAME","HELLO_WORLD");

	// to render the view in normal fashion, you just simply call the function.
	$view->render(true,true);	
?>