/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

const $ = require('jquery');

global.$ = global.jQuery = $;

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import 'animate.css';



// start the Stimulus application
import './bootstrap';

// Vendor CSS 
import './bpay-assets/vendor/bootstrap/css/bootstrap.css';
import './bpay-assets/vendor/font-awesome/css/font-awesome.css';
import './bpay-assets/vendor/magnific-popup/magnific-popup.css';
import './bpay-assets/vendor/bootstrap-datepicker/css/datepicker3.css';

//  Specific Page Vendor CSS 
import './bpay-assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css';
import './bpay-assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css';
import './bpay-assets/vendor/morris/morris.css';

//  Theme CSS 
import './bpay-assets/stylesheets/theme.css';

//  Skin CSS 
import './bpay-assets/stylesheets/skins/default.css';

//  Theme Custom CSS 
import './bpay-assets/stylesheets/theme-custom.css';

import './bpay-assets/vendor/modernizr/modernizr';

//  Vendor 
import './bpay-assets/vendor/jquery/jquery.js';
import './bpay-assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js';
import './bpay-assets/vendor/bootstrap/js/bootstrap.js';
import './bpay-assets/vendor/nanoscroller/nanoscroller.js';
import './bpay-assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js';
import './bpay-assets/vendor/magnific-popup/magnific-popup.js';
import './bpay-assets/vendor/jquery-placeholder/jquery.placeholder.js';

//  Specific Page Vendor 
import './bpay-assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js';
import './bpay-assets/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js';
import './bpay-assets/vendor/jquery-appear/jquery.appear.js';
import './bpay-assets/vendor/bootstrap-multiselect/bootstrap-multiselect.js';
import './bpay-assets/vendor/jquery-easypiechart/jquery.easypiechart.js';
import './bpay-assets/vendor/flot/jquery.flot.js';
import './bpay-assets/vendor/flot-tooltip/jquery.flot.tooltip.js';
import './bpay-assets/vendor/flot/jquery.flot.pie.js';
import './bpay-assets/vendor/flot/jquery.flot.categories.js';
import './bpay-assets/vendor/flot/jquery.flot.resize.js';
import './bpay-assets/vendor/jquery-sparkline/jquery.sparkline.js';
import './bpay-assets/vendor/raphael/raphael.js';
import './bpay-assets/vendor/morris/morris.js';
import './bpay-assets/vendor/gauge/gauge.js';
import './bpay-assets/vendor/snap-svg/snap.svg.js';
import './bpay-assets/vendor/liquid-meter/liquid.meter.js';
import './bpay-assets/vendor/jqvmap/jquery.vmap.js';
import './bpay-assets/vendor/jqvmap/data/jquery.vmap.sampledata.js';
import './bpay-assets/vendor/jqvmap/maps/jquery.vmap.world.js';
import './bpay-assets/vendor/jqvmap/maps/continents/jquery.vmap.africa.js';
import './bpay-assets/vendor/jqvmap/maps/continents/jquery.vmap.asia.js';
import './bpay-assets/vendor/jqvmap/maps/continents/jquery.vmap.australia.js';
import './bpay-assets/vendor/jqvmap/maps/continents/jquery.vmap.europe.js';
import './bpay-assets/vendor/jqvmap/maps/continents/jquery.vmap.north-america.js';
import './bpay-assets/vendor/jqvmap/maps/continents/jquery.vmap.south-america.js';

//  Theme Base, Components and Settings 
import './bpay-assets/javascripts/theme.js';

//  B-PAY CORE JS 
import './bpay-core.js';

//  Theme Custom 
import './bpay-assets/javascripts/theme.custom.js';

//  Theme Initialization Files 
import './bpay-assets/javascripts/theme.init.js';

//  Examples 
import './bpay-assets/javascripts/dashboard/examples.dashboard.js';

