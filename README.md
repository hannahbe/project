project - Website for a small printing house
============================================

The project is a website for the Copy Color Jerusalem company, which is a small printing house.

The diary can be found here: https://trello.com/b/AyLIWAyp/diary-hannah-bellaiche

I used Wordpress and wrote a theme that suits the needs of the company.
The theme I wrote - copycolor - is a child theme of twentyfourteen.
In the file "twentyfourteen" I didn't write any code, but I use some code of it. All the code I wrote is in copycolor.
Works on Firefox, Opera and Google Chrome.

The website displays:
- a main page with a slider
- an About page
- a Cart where the client can see what is in his current cart and display his previous orders invoices
- a Catalog page with an archives widget sidebar on the right
- a Contact page
- a Catalog page divided in 3 subpages: sublimation, design and print
- a Service-Print page where the client can choose a print products (=booklet, simple copies, etc...), upload files, choose options for each file (color/black and white, one side/two sides, ...), choose quantity for each one and save or send his order
- a Service-Sublimation page where the client can choose a sublimation product (=pillow, mug, etc...), create designs with an online designer and receive a live preview of his designs on the product, choose quantities for each design and send or save his order
  
The admin screens allow the workers to manage the website, the orders and the products database.
The worker can add posts to the catalog and the gallery using the Posts screen from Wordpress and using the right category corresponding to the page or subpage: gallery, catalog-print, catalog-sublimation, catalog-design. He can as well add other categories, if the post belongs to the gallery it will be displayed in the widget sidebar, otherwise it will not appear. These categories are not deletable.
The worker can add pages using the Pages screen from Wordpress but the pages that were created for the theme are not deletable.
The worker can modify appearance of the website using the Appeareance -> Customize subscreen in Wordpress that was suited for the theme. He can change logo, background color, etc...
Slider WD plugin screen can be used to modify the slider displayed in the main page.
New screens:
- About: add/delete/modify article of the About page, like workers.
- Sublimation : add/delete/view sublimation products. To add a sublimation product, the worker has to enter regular data like price, photo etc. and place a "sublimation area" (= where the design will be displayed on the product image for live preview) on the photo
- Print: add/delete/view print products and categories
- Orders: manage clients orders

List of files and folders:

- images: all permanent images used for the website, such as the company logo, the button, etc...

inc:- collections of functions relative to the theme's functionality.
	- tcpdf (folder): TCPDF is a free software for generating PDF documents without requiring external extensions.
	- admin-menu.php: a collection of functions relative to the admin screen functionalities. theme security - removal of pages deletion (only for the page I created to avoid a situation where a worker would damage the website by removing a critical page) - , new special screens - add print and sublimation products, view orders, etc... - , etc...
	- cart.php: a collection of functions relative to the client's cart. using php session.
	- catalog-gallery.php : functions relative to the catalog and the gallery - posts display
	- customizer.php: remove and add customize options to the theme's customizer - choice of background color, logo, etc... -
	- database.php : the database layer. function to remove, add, read from the database
	- files-upload.php: functions to sanitize and save files inputs.
	- invoice.php: functions to create pdf invoices. using tcpdf
	- service.php: collection of functions relative to service print and service sublimation - products display etc... -
	- widgets.php: functions to suit the widgets to the company needs.
	
js: - collections of js functions adding functionalities to both sides - admin screens and blog.
	- add-sub.js : globals and functions used in the "Add Sublimation Product" admin screen. SubArea class - the area in the product image the design will be diplayed on.
	- copycolor-customizer.js : add live preview to the customizer.
	- online-designer.js : collection of functions relative to the online designer - globals, mousedown, mouseup, mousemove, keydown listeners, history parser, etc...
	- online-designer-classes.js : CanvasHistory - holds the client's moves in 2 arrays undo and redo -, TextItem - a text in the design, can be rotated, resized, etc -, ImageItem - an image in the design, can be rotated, resized, etc -
	- service-final-step.js : relative to the last screen of print and sublimation screens and the cart - total price changes when the client changes a product quantity etc...
	- service-icons.js : relative to print and sublimation service icons display
	- service-print.js : functionalities relative to the files upload screen in print service
	
tests :
	- carttest.php : tests for the cart. change $PHP_SESSION for an array $SESSION to simulate the functions relative to cart and test their behaviour.
	
- footer.php : the footer
- functions.php : includes all the files from folder "inc" (collection of functions relative to the theme). is loaded along with the parent theme functions.php file (and not instead of)
- header.php: the header
- index.php : template for the main page
- page.php : default template
- page-about.php : template for About
- page-cart.php : template for Cart
- page-catalog.php : template for Catalog
- page-contact.php : template for Contact
- page-gallery.php: template for Gallery
- page-print-service.php : template for Service-Print
- page-sublimation-service.php : template for Service-Sublimation
- style.css : the stylesheet