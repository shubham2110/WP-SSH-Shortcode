# WP-SSH-Shortcode
Fetch Values by SSH the server and executing the command and show in Wordpress excerpts. 

Very badly documented. 


Create a Database Table with Following Details:


CREATE TABLE `wp_basis_portal` (
  `UNIQUE_ID` varchar(50) NOT NULL,
  `LANDSCAPE` varchar(5) NOT NULL,
  `SYSTEM` varchar(12) NOT NULL,
  `SYSTYPE` varchar(20) DEFAULT NULL,
  `IP` varchar(16) NOT NULL,
  `LOGINNAME` varchar(5) NOT NULL DEFAULT 'root',
  `LOGINPASS` varchar(15) NOT NULL,
  `COMMAND` text,
  `TITLE` varchar(20) DEFAULT NULL,
  `THRESHOLD` int(3) DEFAULT NULL,
  `COLTYPE` varchar(10) DEFAULT NULL,
  `ACTIVE` char(10) NOT NULL DEFAULT 'Y',
  `SHIFTLOGTYPE` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


Import the plugin. Now use following shortcode. 

[avihasta_basis uid="someuniqueid"]

This will fetch the IP, LOGINNAME, LAGINPASS from wp_basis_portal table. 

It will login into the machine and execute command specified in 'COMMAND' attribute of database table. 

Fork it to make it better, comment for suggestions. 

