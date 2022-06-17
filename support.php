<?php
@include_once("config.php");
@include_once("api/function.php");

// language selection
$GLOBALS['lang'] = get_lang();
load_translation('api');// Loading system translations
?>

<!DOCTYPE html>
<html lang="<?=$lang;?>">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="robots" content="noindex, nofollow">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>SEACMS | <?php echo __("Support page");?></title>

	<link rel="stylesheet" href="<?=$GLOBALS['path']?>api/assets/css/knacss.min.css?<?=$GLOBALS['cache']?>">
	<link rel="stylesheet" href="<?=$GLOBALS['path']?>api/assets/css/custom<?=$GLOBALS['min']?>.css?<?=$GLOBALS['cache']?>">

	<?php if(@$GLOBALS['favicon']){?><link rel="shortcut icon" type="image/x-icon" href="<?=$GLOBALS['favicon']?>"><?php }?>


	<style>

	body {
		color: var(--main-color);
	}

	summary {
		display: block;
	}

	h1 {
		font-size: 1.65rem;
		color: var(--color-1);
		margin: 2rem 0;
		text-transform: uppercase;
		font-weight: normal;
		letter-spacing: 0.2rem;
		margin-top: 2.3rem;
	}

	h2 {
		font-size: 1.3rem;
		font-weight: normal;
	  margin: 0;
		padding-right: 1rem;
	}

	h3 {
		font-size: 1.2rem;
		margin: 1rem auto 0.5rem;
	}

	p i, p b, li i, li b {
		color: var(--color-1);
	}

	.title {
	  padding: 1rem;
	  border-bottom: solid 1px #bbb;
	  transition: background 1s;
	  cursor: pointer;
	}

	.title:hover {
	  background: var(--light-color);
	}

	details[open] .title {
	  background: var(--light-color);
		border-bottom: 3px solid var(--color-3);
	}

	.content {
	  padding: 1rem;
	}

	.title::after {
		font-family: 'moon';
		content:"\e935";
		font-size: 1.5rem;
		float: right;
		transition: transform 0.5s;
		margin-top: -2.2rem;
	}

	details[open]	.title::after {
		content:"\e931";
	  transition: transform 0.5s;
	}

	</style>

</head>
<body class="bg-blue p-36">

		<div class="layout-maxed grid">

			<div class="card bg-white border-rounded shadow">

				<div class="md:layout-maxed">

					<div class="text-center mt-36 md:flex justify-center">
						<img id="logo" class="img install-img mr-8" alt="logo SeaCms" src="api/assets/img/logo.svg">
						<h1>/ <?php _e("Support page")?> /</h1>
					</div>

				</div>

				<div id="content" class="layout-maxed m-36">
					<details>
						<summary class="title"><h2><i class="icon moon-edit" aria-hidden="true"></i> <?php _e("Edition mode")?></h2></summary>
						<div id="edition_mode" class="content">
							<h3><?php _e("log in to edit mode")?></h3>
							<ul>
								<li><?php _e("Click on the <b>key</b> icon <i class='icon icon-bolder moon-key'></i> at the bottom left of the screen (displayed when hovering)")?>.</li>
							  <li><?php _e("Enter the email and the password that was sent to you")?>.</li>
							</ul>
							<h3><?php _e("Exit edit mode")?></h3>
							<p><?php _e("To have an overview of additions, changes, deletions, you must leave the editing mode by clicking on the <b>cross</b> icon <i class='icon icon-bolder moon-x'></i> at the top right")?>.</p>
							<h3><?php _e("Log out")?></h3>
							<p><?php _e("To log out, hover over the <b>user</b> icon <i class='icon icon-bolder moon-user'></i> at the top left and click on the <b>log out</b> icon <i class='icon icon-bolder moon-log-out'></i>")?>.</p>
						</div>
					</details>
					<details>
						<summary class="title"><h2><i class="icon moon-file-text" aria-hidden="true"></i> <?php _e("Managing content")?></h2></summary>
						<div id="content_management"class="content">
							<h3><?php _e("Add content (page,article,event,...)")?></h3>
							<ul>
							 	<li><?php _e("Click on the <b>plus</b> icon <i class='icon icon-bolder moon-plus'></i> at the bottom left (appears on hover) to bring up the window")?>.</li>
								<li><?php _e("Click on the tab corresponding to the desired content")?>.</li>
								<li><?php _e("Add a title")?>.</li>
								<li><?php _e("Select a model adapted to your needs from the drop-down list")?>.</li>
								<li><?php _e("Validate by clicking on ok")?>.</li>
								<li><?php _e("Add content and click on save at the top right")?>.</li>
								<li><?php _e("Don't forget to activate the visible mode by clicking on the <b>crossed out eye</b> icon <i class='icon icon-bolder moon-eye-off'></i> when the content is ready to be published")?>.</li>
							</ul>

							<h3><?php _e("Enter the useful data for referencing")?></h3>
							<ul>
							  <li> <?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
							  <li><?php _e("Hover over the page title in the top bar to see the page data")?>.</li>
							  <li><?php _e("Enter the title of the page, this is what will appear in search engines")?>.</li>
							  <li><?php _e("Fill in the field <b>Description for search engines</b>, it corresponds to the few lines that will appear under the title in search engines")?>.</li>
							  <li><?php _e("If necessary, check <b>noindex</b> to prevent the page from being indexed by search engines (for example for legal notices)")?>.</li>
							  <li><?php _e("If necessary, check <b>nofollow</b> so that the links of this page are not followed by search engines")?>.</li>
							  <li><?php _e("You can modify the formatted web address, it corresponds to the url of your page")?>.</li>
							  <li><?php _e("You can add an image, it will be displayed when sharing your page on social networks")?>.</li>
							  <li><?php _e("Click on save at the top right")?>.</li>
							</ul>

							<h3><?php _e("Add content")?></h3>
							<ul>
					    	<li><?php _e("After the creation of content (page, article, event,...) the editing mode is automatically activated")?>.</li>
								<li><?php _e("Click on an editable area and add the desired content")?>.</li>
								<li><?php _e("To add an image, click on the grey area and select the image to upload")?>.</li>
								<li><?php _e("Click on save at the top right")?>.</li>
							</ul>

							<h3><?php _e("Add categories or tags in an event or an article")?></h3>
							<ul>
								<li><?php _e("Go to the event or article concerned")?>.</li>
								<li><?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
							  <li><?php _e("Click on the field below or next to <b>Categories</b> or <b>Tags</b>")?>.</li>
							  <li><?php _e("Choose a category or tag from the drop-down list or enter a new one")?>.</li>
							  <li><?php _e("To enter several, separate them with a comma")?>.</li>
							</ul>

							<h3><?php _e("Put a content online or offline (draft)")?></h3>
							<ul>
								<li><?php _e("Go to the page concerned")?>.</li>
							  <li><?php _e("Click on the <b>eye</b> <i class='icon icon-bolder moon-eye'></i>/ <b>crossed out eye</b> icon <i class='icon icon-bolder moon-eye-off'></i> at the top right of the top bar to switch the page online or offline (draft)")?>.</li>
							  <li><?php _e("Click on save at the top right")?>.</li>
							</ul>

							<h3><?php _e("Update content")?></h3>
					    <ul>
								<li><?php _e("Go to the content concerned (page, article, event, ...)")?>.</li>
							  <li><?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
							  <li><?php _e("Or click on the <b>menu</b> icon <i class='icon icon-bolder moon-menu'></i> at the top left and then click on the content concerned (page, article, event...)")?>.</li>
							  <li><?php _e("Click on the area to be modified, make the desired changes. You can format the text, the toolbar appears when you click in the area")?>.</li>
							  <li><?php _e("Click on save at the top right")?>.</li>
							</ul>

							<h3><?php _e("Change the date of event or article (to change the order)")?></h3>
							<ul>
								<li><?php _e("Go to the event or article concerned")?>.</li>
							  <li><?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
							  <li><?php _e("Hover over the page title in the top bar")?>.</li>
							  <li><?php _e("Modify the creation date")?>.</li>
							</ul>

							<h3><?php _e("Delete a content (page, article, event, ...)")?></h3>
					    <ul>
				        <li><?php _e("Go to the content concerned (page, article, event, ...)")?>.</li>
				        <li><?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
				        <li><?php _e("Click on delete at the top right")?>.</li>
				        <li><?php _e("Click on <b>OK</b> to confirm")?>.</li>
					    </ul>

						</div>
					</details>
					<details>
						<summary class="title"><h2><i class="icon moon-sliders" aria-hidden="true"></i> <?php _e("Formatting content")?></h2></summary>
						<div id="formatting_content" class="content">
							<h3><?php _e("Format a text as a title")?></h3>
							<ul>
					    	<li><?php _e("Select the text that will become a title")?>.</li>
								<li><?php _e("In the toolbar that appears, click on <b>T²</b> (2nd level title), <b>T³</b> (3rd level title), etc")?>.</li>
							</ul>

							<h3><?php _e("Put a term in bold")?></h3>
							<ul>
						    <li><?php _e("Select the word or phrase to be bolded")?>.</li>
						    <li><?php _e("In the toolbar that appears, click on <b>bold</b> icon <i class='icon icon-bolder moon-bold'></i>")?>.</li>
							</ul>

					    <h3><?php _e("Put a term in italic")?></h3>
					    <ul>
				        <li><?php _e("Select the word or phrase to be italicized")?>.</li>
				        <li><?php _e("In the toolbar that appears, click on <b>italic</b> icon <i class='icon icon-bolder moon-italic'></i>")?>.</li>
					    </ul>

				    	<h3><?php _e("Add a quote")?></h3>
							<ul>
							  <li><?php _e("Select the phrase that will become a quote")?>.</li>
							  <li><?php _e("In the toolbar that appears, click on <b>quote</b> icon <i class='icon icon-bolder moon-quote'></i>")?>.</li>
							</ul>

							<blockquote><?php _e("This is an example of a quote")?>.</blockquote>

					    <h3><?php _e("Add a bulleted list")?></h3>
							<ul>
						    <li><?php _e("Type the text and press <b>Enter</b> on each new line")?>.</li>
						    <li><?php _e("Select all the text")?>.</li>
						    <li><?php _e("In the toolbar that appears, click on <b>bulleted list</b> icon <i class='icon icon-bolder moon-list'></i>")?>.</li>
							</ul>

				    	<h3><?php _e("Align the text")?></h3>
							<ul>
							  <li><?php _e("Select the text to be aligned")?>.</li>
							  <li><?php _e("In the toolbar that appears, click on <b>align left</b> icon <i class='icon icon-bolder moon-align-left'></i>, on <b>align center</b> icon <i class='icon icon-bolder moon-align-center'></i> or on <b>align right</b> icon <i class='icon icon-bolder moon-align-right'></i>")?>.</li>
							</ul>

				    	<h3><?php _e("Avoid isolated words")?></h3>
							<ul>
						    <li><?php _e("In the toolbar that appears, click on <b>code</b> icon <i class='icon icon-bolder moon-code'></i> to display the source code")?>.</li>
						    <li><?php _e("Place your cursor between the two words you want to see on the same line. Delete the space between these two words, they must be pasted")?>.</li>
						    <li><?php _e("Add the HTML code <b>&amp;nbsp;</b> to have a non-breaking space")?>.</li>
							</ul>

							<h3><?php _e("Add a icon")?></h3>
							<ul>
						    <li><?php _e("Position yourself at the place where you want to insert an icon")?>.</li>
						    <li><?php _e("In the toolbar that appears, click on <b>flag</b> icon <i class='icon icon-bolder moon-flag'></i> to display the icon library")?>.</li>
							</ul>

				    	<h3><?php _e("Add a hyperlink")?></h3>
							<ul>
						    <li><?php _e("Select the text for which you want to add a hyperlink")?>.</li>
						    <li><?php _e("In the toolbar that appears, click on <b>link</b> icon <i class='icon icon-bolder moon-link'></i>")?>.</li>
						    <li><?php _e("For an internal page, enter the first letter(s) of the page and choose the page from the drop-down menu that appears")?>.</li>
						    <li><?php _e("For an external page, enter the address (url) of the link")?>.</li>
						    <li><?php _e("If necessary, click on the <b>log out</b> icon <i class='icon icon-bolder moon-log-out'></i> to make the link look like a button")?>.</li>
						    <li><?php _e("If necessary, click on the <b>external link</b> icon <i class='icon icon-bolder moon-external-link'></i> to open the link in another tab")?>.</li>
						    <li><?php _e("Click on <b>Add link</b>")?>.</li>
							</ul>

					    <h3><?php _e("Highlighting text")?></h3>
							<ul>
						    <li><?php _e("Select the text to highlight")?>.</li>
					    	<li><?php _e("In the toolbar that appears, click on <b>star</b> icon <i class='icon icon-bolder moon-star'></i>")?>.</li>
							</ul>

							<div class="highlight"><?php _e("Example of highlighted text")?>.</div>
						</div>
					</details>
					<details>
						<summary class="title"><h2><i class="icon moon-image" aria-hidden="true"></i> <?php _e("Managing images")?></h2></summary>
						<div id="pictures_management" class="content">
							<h3><?php _e("Add an image to a text box")?></h3>
							<ul>
								<li><?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
							  <li><?php _e("Position yourself at the desired location in the text box")?>.</li>
							  <li><?php _e("In the toolbar that appears, click on <b>image</b> icon <i class='icon icon-bolder moon-image'></i>")?>.</li>
							  <li><?php _e("If necessary, resize the image by clicking on the bottom right corner of the image")?>.</li>
							  <li><?php _e("If needed, click on <b>subtitle</b> to add an alt attribute (useful for accessibility and referencing)")?>.</li>
								<li><?php _e("Enter the information")?>.</li>
							  <li><?php _e("Click on save at the top right")?>.</li>
							</ul>

							<h3><?php _e("Add an image caption")?></h3>
							<ul>
								<li><?php _e("Sometimes a text field will allow you to add a caption to the image (e.g., credits, source, etc.)")?>.</li>
								<li><?php _e("Enter the information")?>.</li>
								<li><?php _e("Click on save at the top right")?>.</li>
							</ul>

						</div>
					</details>

					<details>
						<summary class="title"><h2><i class="icon moon-life-buoy" aria-hidden="true"></i> <?php _e("Managing modules")?></h2></summary>
						<div id="module_management" class="content">
							<h3><?php _e("Add a module")?></h3>
							<ul>
						    <li><?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
						    <li><?php _e("Click on the <b>Add a module</b> button")?>.</li>
						    <li><?php _e("Enter the content, the information of the module (text, image...)")?>.</li>
						    <li><?php _e("Click on save at the top right")?>.</li>
							</ul>
					    <h3><?php _e("Move a module")?></h3>
							<p><?php _e("Once several modules are added, it is possible to move them:")?></p>
							<ul>
						    <li><?php _e("Click on the <b>Move</b> button")?>.</li>
						    <li><?php _e("Drag and drop the module(s) to be moved")?>.</li>
							</ul>
					    <h3><?php _e("Delete a module")?></h3>
					    <p><?php _e("Click on the <b>cross</b> icon <i class='icon icon-bolder moon-x'></i> in the top right corner of the concerned module(s)")?>.</p>
						</div>
					</details>

					<details>
						<summary class="title"><h2><i class="icon moon-menu" aria-hidden="true"></i> <?php _e("Managing menus and sub-menus")?></h2></summary>
						<div id="menu_management" class="content">
							<h3><?php _e("Add a navigation menu")?></h3>
							<ul>
								<li><?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
					    	<li><?php _e("Position yourself under the top bar and click on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> (displayed on hover)")?>.</li>
							  <li><?php _e("Click on the <b>plus</b> icon <i class='icon icon-bolder moon-plus'></i> next to each item that needs to be added to the menu")?>.</li>
							  <li><?php _e("To change the order of the menu, position yourself on the gray area below the item concerned, click when the small hand appears and drag the item")?>.</li>
							</ul>

						  <h3><?php _e("Delete a menu item")?></h3>
				 			<p><?php _e("To remove an item from the menu, position yourself on the grey area under the concerned item and click on the <b>cross</b> icon <i class='icon icon-bolder moon-x'></i>")?>.</p>

						</div>
					</details>

					<details>
						<summary class="title"><h2><i class="icon moon-users" aria-hidden="true"></i> <?php _e("Managing users")?></h2></summary>
						<div id="users_management" class="content">
							<h3><?php _e("Add a user")?></h3>
							<ul>
								<li><?php _e("Activate the editing mode by clicking on the <b>pencil</b> icon <i class='icon icon-bolder moon-edit'></i> at the bottom left of the screen (displayed on hover)")?>.</li>
				    		<li><?php _e("On the top bar, click on the <b>user</b> icon <i class='icon icon-bolder moon-user'></i> then on the <b>add a user</b> icon <i class='icon icon-bolder moon-user-plus'></i>")?></li>
							  <li><?php _e("Select the rights to be assigned to this user (hold down the CTRL key to select several)")?>.</li>
							  <li><?php _e("Enter a username and an email address")?>.</li>
							  <li><?php _e("Click on the <b>envelope</b> icon <i class='icon icon-bolder moon-mail'></i> to send the password by email")?>.</li>
							  <li><?php _e("Click on save at the top right")?>.</li>
							</ul>

							<h3><?php _e("Modify a user")?></h3>
							<ul>
					    	<li><?php _e("On the top bar, click on the <b>user</b> icon <i class='icon icon-bolder moon-user'></i> then on the <b>user group</b> icon <i class='icon icon-bolder moon-users'></i> to see the list of all users")?>.</li>
							  <li><?php _e("Click on the user to modify")?>.</li>
							  <li><?php _e("To update the username, email address or password, simply change them in the appropriate field")?>. </li>
							  <li><?php _e("To update user rights (permissions), select or deselect the relevant rights (hold down the CTRL key to select multiple rights)")?>.</li>
							  <li><?php _e("Click on save at the top right")?>.</li>
							</ul>

							<h3><?php _e("Delete a user")?></h3>
							<ul>
								<li><?php _e("On the top bar, click on the <b>user</b> icon <i class='icon icon-bolder moon-user'></i> then on the <b>user group</b> icon <i class='icon icon-bolder moon-users'></i> to see the list of all users")?>.</li>
						    <li><?php _e("Click on the user to delete")?>.</li>
						    <li><?php _e("Click on the <b>trash</b> icon <i class='icon icon-bolder moon-trash'></i> at the bottom left of the popup")?>.</li>
						    <li><?php _e("Validate by clicking on ok")?>.</li>
							</ul>
						</div>
					</details>

				</div>

			</div>

		</div>

</body>
</html>
