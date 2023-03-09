# Training Registration Plugin v3
The Training Registration Plugin is a WordPress plugin created to allow SOT training organizers to create training registration forms where Learning Centers could register to. This plugin does require preliminary setup on the WordPress. For example, user login control and theme customization is necessary.
## Features
**For Training Organizers:**
* Create training events
* Post training information to Learning Centers
* Manage the status of trainings including registration open and close time as well as its activation status
* View registered trainees online
* Manage registered trainees
* Download training registration data as Excel Spreadsheet

**For Learning Centers:**
* Create and manage staff profile
* Register to open trainings
* Manage training registrations from its own Learning Center
## Installation
**Install Plugin**
1. Go to `Settings` > `Permalinks` and set it to "Post Name"
2. Download the plugin .zip from the [release page](https://github.com/Siriu5J/Training-Registration/releases).
3. Install the plugin to WordPress by using local upload.

**Customize Site**
1. Make sure you are using the default Twenty Twenty Theme
2. Go to `Admin Control Panel` > `Appearance` > `Customization`.
3. Go to `Site Identity` and change the Site Title and Tagline to something related to training registration
4. Go to `Colors` and set Background Color to `#f8f8f8` and Header & Footer Background Color to `#012552`
5. Go to `Cover Template` and set Overlay Background Color to `#fecc00` and Overlay Text Color to `#ffffff`
6. Remove all menues and widgets
7. Add the following CSS code to `Additional CSS`:
```
button:not(.toggle),
.button,
.faux-button,
.wp-block-button__link,
.wp-block-file .wp-block-file__button,
input[type=”button”], input[type=”reset”], input[type=”submit”],
input[type="Submit"],
.bg-accent,
.bg-accent-hover:hover,
.bg-accent-hover:focus,
:root .has-accent-background-color, .comment-reply-link {
background-color: #fecc00;
color: #012552 !important;
}
a:not(.wp-block-button__link){
	color:#fecc00 !important;
	color:#012552;
}
.wp-block-button__link:hover{
	color: #012552;
}
.entry-title {
	font-size: 35pt;
}
.singular .entry-header{
	padding-top: 0;
	padding-bottom: 1.5rem;
}
}
html, body {
	height: 100%;
}
.home #site-footer {
	position: fixed;
	bottom: 0;
	width: 100%;
}
.header-navigation-wrapper {
	display: none;
}
.header-inner {
	padding-bottom: 0px;
}
```

## Additional Libraries Used/Thanks
* [PHPExcel](https://github.com/PHPOffice/PHPExcel)
