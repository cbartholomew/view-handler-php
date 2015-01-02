<?php
/* view-handler.php
 * Author: Christopher Bartholomew
 * Description:
 * 
 * The view-handler library is an interface/class that assists in dynamic rendering / replacement of php template files.
 * This is favorable to those who don't necessarily want consume themselves with a new framework, and they would like a
 * simple way to have that MVC feel. This library is a simple setup and allows for any customzation. 
 * 
 * // set the paths up in view-configuration.php for where the views are located.
 * 
 * // then create a custom key to identify the view - provide the path including file extension.
 * $view = new ViewHandler("HELLO_WORLD","helloworld.php");
 *
 * // add view arguments that you defined in the hello world template, such as YOUR_NAME and VIEW_NAME
 * // note - you don't need to add the hashes (#) here, but you do need to add them in the template.
 * $view->addViewArgument("YOUR_NAME","Christopher Bartholomew");
 * $view->addViewArgument("VIEW_NAME","HELLO_WORLD");
 *
 * // Case 1 - to render the view in normal fashion, you just simply call the function.
 * $view->render(true,true, false);	
 *
 * // Case 2 - if you want to render the content only, without the header or footer then
 * // set the first two parameters to false (or leave out)
 * $view->render(false,false, false);	
 * 
 * // Case 3 - if you want the function to return the final content WITHOUT rendering it then 
 * // set the third parameter to true. 
 * $myHtml = $view->render(true,true,true);
 */

require("view-configuration.php");

interface IViewHandler
{
	public function render($displayHeader, $displayFooter,$noRender);
	public function addViewArgument($key, $value);
	public function removeViewArgument($key);
	public function printViewArguments();
}

class ViewHandler implements IViewHandler
{
	private $_viewName;
	private $_viewPath;
	private $_viewArguments;	
	protected $_viewDetails;

	function __construct($viewName, $viewPath) {		
			$this->_viewArguments = array();
			$this->_viewName 	  = $viewName;	
			$this->_viewPath 	  = $viewPath;
			
			return $this;
	}
	
	public function addViewArgument($key, $value) {
		array_push($this->_viewArguments,ViewHandler::makeViewArgument($key,$value));
	}

	public function removeViewArgument($key) {
		for ($i=0; $i < count($this->_viewArguments); $i++) { 
			if(strcmp($this->_viewArguments[$i]["key"],$key) == 0){
				unset($this->_viewArguments[$i]);
			}
		}
	}

	public function printViewArguments(){
		foreach ($this->_viewArguments as $argument) {
			print_r($argument);
		}
	}

	public function render($displayHeader, $displayFooter, $noRender){
		$this->setViewDetails();
		$output = "";
		if($displayHeader || $displayFooter)
				$output = $this->renderViewHTML($displayHeader,$displayFooter);
		else
				$output = $this->renderView();	

		if($noRender)
			return $output;
		else
			echo $output;
	}

	private static function makeViewArgument($key, $value) {
		return array(
			"key"   => $key, 
			"value" => $value			
		); 
	}

	private function setViewDetails() {
		$this->_viewDetails = array(
			HEADER_VIEW 	 => VIEW_FOLDER_PATH . VIEW_DEFAULT_HEADER,
			$this->_viewName => VIEW_FOLDER_PATH . $this->_viewPath,
			FOOTER_VIEW 	 => VIEW_FOLDER_PATH . VIEW_DEFAULT_FOOTER
		);
	}	

	private function renderView() {
		// if template exists, render it
		if (file_exists($this->_viewDetails[$this->_viewName])) {
		    // extract variables into local scope
		    extract($arguments);
		
		    // render header
		    require($this->_viewDetails[HEADER_VIEW]);
		
		    // render template
		    require($this->$_viewDetails[$this->_viewName]);
		
		    // render footer
		    require($this->_viewDetails[FOOTER_VIEW]);
		}			
		// else err
		else {
		    trigger_error("Invalid View: $this->_viewName, Path: $this->_viewDetails[$this->_viewName]", E_USER_ERROR);
		}	
	}

	private function renderViewHTML($includeHeader, $includeFooter) {
		$html = "";
		
		// if template exists, render it
		if (file_exists($this->_viewDetails[$this->_viewName])) {
			if($this->_viewArguments >= 1)
		    	// extract variables into local scope
		    	extract($this->_viewArguments);
			
			if($includeHeader)
		    	// get header
		    	$html .= file_get_contents($this->_viewDetails[HEADER_VIEW]);
			
		    	// get template
		    	$html .= file_get_contents($this->_viewDetails[$this->_viewName]);
			
			if($includeFooter)
		    	// get footer
		    	$html .= file_get_contents($this->_viewDetails[FOOTER_VIEW]);
		
			// run replaces based on arguments
			foreach($this->_viewArguments as $argument) {
				$html = str_replace("#" . $argument["key"] . "#", $argument["value"], $html);				
			}
		}			
		// else err
		else {
		    trigger_error("Invalid View: $this->_viewName, Path: $this->_viewDetails[$this->_viewName]", E_USER_ERROR);
		}
		
		return $html;		
	}
}
?>