/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
  config.height = '35em'; 
  config.extraPlugins = 'btgrid';
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
  config.toolbar = [
   ['Styles','Format','Font','FontSize','-','Bold','Italic','Underline','StrikeThrough','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
   '/',
   ['Undo','Redo','-','Cut','Copy','Paste','Find','-','Outdent','Indent','-','Print','-','NumberedList','BulletedList','-','Link','TextColor','BGColor','Source'],
  ] ;      
};