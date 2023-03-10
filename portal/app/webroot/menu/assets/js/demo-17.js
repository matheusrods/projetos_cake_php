 
jQuery( document ).ready(function() {
	jQuery( "#scoop" ).scoopmenu({
		themelayout: 'vertical',
		verticalMenuplacement: 'left',		// value should be left/right
		verticalMenulayout: 'wide',   		// value should be wide/box/widebox
		MenuTrigger: 'click',
		SubMenuTrigger: 'click',
		activeMenuClass: 'active',
		ThemeBackgroundPattern: 'pattern6',
		HeaderBackground: 'theme2' ,
		LHeaderBackground :'theme3',
		NavbarBackground: 'theme3',
		ActiveItemBackground: 'theme0',
		SubItemBackground: 'theme2',
		ActiveItemStyle: 'style0',
		ItemBorder: true,
		ItemBorderStyle: 'solid',
		SubItemBorder: true,
		DropDownIconStyle: 'style1', // Value should be style1,style2,style3
		FixedNavbarPosition: false,
		FixedHeaderPosition: true,
		collapseVerticalLeftHeader: true,
		VerticalSubMenuItemIconStyle: 'style6',  // value should be style1,style2,style3,style4,style5,style6
		VerticalNavigationView: 'view1',
		verticalMenueffect:{
			desktop : "shrink",
			tablet : "push",
			phone : "overlay",
		},
		defaultVerticalMenu: {
			desktop : "sub-expanded",	// value should be offcanvas/collapsed/expanded/compact/compact-acc/fullpage/ex-popover/sub-expanded
			tablet : "collapsed",		// value should be offcanvas/collapsed/expanded/compact/fullpage/ex-popover/sub-expanded
			phone : "offcanvas",		// value should be offcanvas/collapsed/expanded/compact/fullpage/ex-popover/sub-expanded
		},
		onToggleVerticalMenu : {
			desktop : "collapsed",		// value should be offcanvas/collapsed/expanded/compact/fullpage/ex-popover/sub-expanded
			tablet : "expanded",		// value should be offcanvas/collapsed/expanded/compact/fullpage/ex-popover/sub-expanded
			phone : "expanded",			// value should be offcanvas/collapsed/expanded/compact/fullpage/ex-popover/sub-expanded
		},

	});


	/* Left header Theme Change function Start */
	function handleleftheadertheme() {
		jQuery('.theme-color > a.leftheader-theme').on("click", function() {
			var lheadertheme = jQuery(this).attr("lheader-theme");
			jQuery('.scoop-header .scoop-left-header').attr("lheader-theme", lheadertheme);
        });
    };

	handleleftheadertheme();
 /* Left header Theme Change function Close */
 /* header Theme Change function Start */
	function handleheadertheme() {
		jQuery('.theme-color > a.header-theme').on("click", function() {
			var headertheme = jQuery(this).attr("header-theme");
			jQuery('.scoop-header').attr("header-theme", headertheme);
        });
    };
	handleheadertheme();
 /* header Theme Change function Close */
 /* Navbar Theme Change function Start */
	function handlenavbartheme() {
		jQuery('.theme-color > a.navbar-theme').on("click", function() {
			var navbartheme = jQuery(this).attr("navbar-theme");
			jQuery('.scoop-navbar').attr("navbar-theme", navbartheme);
        });
    };

	handlenavbartheme();
 /* Navbar Theme Change function Close */
 /* Active Item Theme Change function Start */
	function handleactiveitemtheme() {
		jQuery('.theme-color > a.active-item-theme').on("click", function() {
			var activeitemtheme = jQuery(this).attr("active-item-theme");
			jQuery('.scoop-navbar').attr("active-item-theme", activeitemtheme);
        });
    };

	handleactiveitemtheme();
 /* Active Item Theme Change function Close */
 /* SubItem Theme Change function Start */
	function handlesubitemtheme() {
		jQuery('.theme-color > a.sub-item-theme').on("click", function() {
			var subitemtheme = jQuery(this).attr("sub-item-theme");
			jQuery('.scoop-navbar').attr("sub-item-theme", subitemtheme);
        });
    };

	handlesubitemtheme();
 /* SubItem Theme Change function Close */
 /* Theme background pattren Change function Start */
	function handlethemebgpattern() {
		jQuery('.theme-color > a.themebg-pattern').on("click", function() {
			var themebgpattern = jQuery(this).attr("themebg-pattern");
			jQuery('body').attr("themebg-pattern", themebgpattern);
        });
    };

	handlethemebgpattern();
 /* Theme background pattren Change function Close */
 /* Vertical Navigation View Change function start*/
	function handleVerticalNavigationViewChange() {
		jQuery('#navigation-view').val('view1').on('change', function (get_value) {
			get_value = jQuery(this).val();
			jQuery('.scoop').attr('vnavigation-view', get_value);
		});
	};

   handleVerticalNavigationViewChange ();
 /* Theme Layout Change function Close*/
 /* Theme Layout Change function start*/
	function handlethemeverticallayout() {
		jQuery('#theme-layout').val('wide').on('change', function (get_value) {
			get_value = jQuery(this).val();
			jQuery('.scoop').attr('vertical-layout', get_value);
		});
	};

   handlethemeverticallayout ();
 /* Theme Layout Change function Close*/
 /* Menu effect change function start*/
	function handleverticalMenueffect() {
		jQuery('#vertical-menu-effect').val('shrink').on('change', function (get_value) {
			get_value = jQuery(this).val();
			jQuery('.scoop').attr('vertical-effect', get_value);
		});
	};

   handleverticalMenueffect ();
 /* Menu effect change function Close*/
 /* Vertical Menu Placement change function start*/
   function handleverticalMenuplacement() {
		jQuery('#vertical-navbar-placement').val('left').on('change', function (get_value) {
			get_value = jQuery(this).val();
			jQuery('.scoop').attr('vertical-placement', get_value);
			jQuery('.scoop-navbar').attr("scoop-navbar-position", 'absolute' );
			jQuery('.scoop-header .scoop-left-header').attr("scoop-lheader-position", 'relative' );
		});
	};

   handleverticalMenuplacement ();
 /* Vertical Menu Placement change function Close*/
 /* Vertical Active Item Style change function Start*/
   function handleverticalActiveItemStyle() {
		jQuery('#vertical-activeitem-style').val('style1').on('change', function (get_value) {
			get_value = jQuery(this).val();
			jQuery('.scoop-navbar').attr('active-item-style', get_value);
		});
	};

   handleverticalActiveItemStyle ();
 /* Vertical Active Item Style change function Close*/
 /* Vertical Item border change function Start*/
	function handleVerticalIItemBorder() {
			jQuery('#vertical-item-border').change(function() {
				if( jQuery(this).is(":checked")) {
					jQuery('.scoop-navbar .scoop-item').attr('item-border', 'false');
				}else {
					jQuery('.scoop-navbar .scoop-item').attr('item-border', 'true');
				}
			});
		};

   handleVerticalIItemBorder ();
 /* Vertical Item border change function Close*/
 /* Vertical SubItem border change function Start*/
   function handleVerticalSubIItemBorder() {
			jQuery('#vertical-subitem-border').change(function() {
				if( jQuery(this).is(":checked")) {
					jQuery('.scoop-navbar .scoop-item').attr('subitem-border', 'false');
				}else {
					jQuery('.scoop-navbar .scoop-item').attr('subitem-border', 'true');
				}
			});
		};

   handleVerticalSubIItemBorder ();
 /* Vertical SubItem border change function Close*/
 /* Vertical Item border Style change function Start*/
   function handleverticalboderstyle() {
		jQuery('#vertical-border-style').val('solid').on('change', function (get_value) {
			get_value = jQuery(this).val();
			jQuery('.scoop-navbar .scoop-item').attr('item-border-style', get_value);
		});
	};

   handleverticalboderstyle ();
 /* Vertical Item border Style change function Close*/
 /* Vertical Dropdown Icon change function Start*/
      function handleVerticalDropDownIconStyle() {
		jQuery('#vertical-dropdown-icon').val('style1').on('change', function (get_value) {
			get_value = jQuery(this).val();
			jQuery('.scoop-navbar .scoop-hasmenu').attr('dropdown-icon', get_value);
		});
	};

   handleVerticalDropDownIconStyle ();
 /* Vertical Dropdown Icon change function Close*/
 /* Vertical SubItem Icon change function Start*/

    function handleVerticalSubMenuItemIconStyle() {
		jQuery('#vertical-subitem-icon').val('style5').on('change', function (get_value) {
			get_value = jQuery(this).val();
			jQuery('.scoop-navbar .scoop-hasmenu').attr('subitem-icon', get_value);
		});
	};

   handleVerticalSubMenuItemIconStyle ();
 /* Vertical SubItem Icon change function Close*/
 /* Vertical Navbar Position change function Start*/
	function handlesidebarposition() {
			jQuery('#sidebar-position').change(function() {
				if( jQuery(this).is(":checked")) {
					jQuery('.scoop-navbar').attr("scoop-navbar-position", 'fixed' );
					jQuery('.scoop-header .scoop-left-header').attr("scoop-lheader-position", 'fixed' );
				}else {
					jQuery('.scoop-navbar').attr("scoop-navbar-position", 'absolute' );
					jQuery('.scoop-header .scoop-left-header').attr("scoop-lheader-position", 'relative' );
				}
			});
		};

   handlesidebarposition ();
 /* Vertical Navbar Position change function Close*/
 /* Vertical Header Position change function Start*/
   	function handleheaderposition() {
			jQuery('#header-position').change(function() {
				if( jQuery(this).is(":checked")) {
					jQuery('.scoop-header').attr("scoop-header-position", 'fixed' );
					jQuery('.scoop-main-container').css('margin-top', jQuery(".scoop-header").outerHeight());
				}else {
					jQuery('.scoop-header').attr("scoop-header-position", 'relative' );
					jQuery('.scoop-main-container').css('margin-top', '0px');
				}
			});
		};

   handleheaderposition ();
 /* Vertical Header Position change function Close*/


/*  collapseable Left Header Change Function Start here*/
   	function handlecollapseLeftHeader() {
			jQuery('#collapse-left-header').change(function() {
				if( jQuery(this).is(":checked")) {
					jQuery('.scoop-header, .scoop ').removeClass('iscollapsed');
					jQuery('.scoop-header, .scoop').addClass('nocollapsed');
				}else {
					jQuery('.scoop-header, .scoop').addClass('iscollapsed');
					jQuery('.scoop-header, .scoop').removeClass('nocollapsed');
				}      
			});
		};

   handlecollapseLeftHeader ();


/*  collapseable Left Header Change Function Close here*/
 
  
});
