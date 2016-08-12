# Stat4PHP
A PHP Library to connect to and download data from the getstat.com JSON API - developed my Matt Stannard (@mattstannard - https://twitter.com/mattstannard), Director of Innovlation - 4Ps Marketing - www.4psmarketing.com

This library current is READ only.

Introduction
==============
This library was created to help pull keyword data from Stat into a data warehouse.

When developing it we thought it may be helpful for others to use or add to hence pushing it to GIT and making it public.

Please do send comments to matt.stannard@4psmarketing.com - its not perfect yet but hopefully it should make things easier

Enjoy :)

Example Usage
==============

Getting Started
--------------
You will need your STAT API Key and STAT Sub Domain to use this library, if you are not a STAT user then sign up, it's awesome!

Also, the _STAT_API_KEY and _STAT_SUBDOMAIN need to be passed to the StatProject and / or StatSite object if you want to pull data, I intened
to make the StatWrapper passed into the constructor of each to make this a bit better.

Getting All Projects
--------------
$MyStat = new StatWrapper();  
$MyStat->_STAT_API_KEY = "YOUR API KEY";  
$MyStat->_STAT_SUBDOMAIN = "YOUR SUBDOMAIN";  

$arrProjects = $MyStat->GetProjects();  

foreach($arrProjects as $p)  
{  
    echo "This is my STAT Project Name " . $p->Name . "\n";  
}  

Getting All Sites
--------------
$MyStat = new StatWrapper();  
$MyStat->_STAT_API_KEY = "YOUR API KEY";  
$MyStat->_STAT_SUBDOMAIN = "YOUR SUBDOMAIN";  

$arrSites = $MyStat->GetSites();  

foreach($arrSites as $s)  
{  
    echo "This is my STAT Site Title " . $s-Title . "\n";  
}  

Getting Keywords for a Site by its Id
--------------
$MyStat = new StatWrapper();  
$MyStat->_STAT_API_KEY = "YOUR API KEY";  
$MyStat->_STAT_SUBDOMAIN = "YOUR SUBDOMAIN";  

$s = new StatSite();  
$s->SiteId = 12345;  
$s->_STAT_API_KEY = $MyStat->_STAT_API_KEY;  
$s->_STAT_SUBDOMAIN = $MyStat->_STAT_SUBDOMAIN;  
$s->GetKeywords(true);  

foreach($s->Keywords as $kw)  
	{  
		foreach($kw->Rankings as $sr)  
		{  
		echo "Keyword " . $kw->Keyword . " ranks " . $sr->RankValue . " in " . $sr->RankType . "\n";  
		}  
	}  
