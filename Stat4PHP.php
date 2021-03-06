<?php
/**
 * PHP Wrapper for the Stat (getstat.com) JSON API
 *
 * These classes provie a PHP Wrapper to the Stat (getstat.com) 
 *
 *
 * @category   Software
 * @package    fourps.stat
 * @author     Matt Stannard <matt.stannard@4psmarketing.com>
 * @copyright  1997-2016 4Ps Marketing
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 */

 
/**
 *
 * 	Class : 		StatWrapper
 *
 *	Description : 	
 *
 *	Provides a simple wrapper which makes web requests to the Stat API returning data as JSON
 *	Also has helper functions to return all projects, sites or a project by name
*/
class StatWrapper
{
	// Declare two member variables for the API KEY and the SUBDOMAIN
	public $_STAT_API_KEY;
	public $_STAT_SUBDOMAIN;
	
	/**
	 *	Function : 		GetStatAPIUrl
	 *
	 *	Description : 	Gets the API URL using the API_KEY and SUBDOMAIN
	 *
	 */
	public function GetStatAPIUrl()
	{
		$strURL = "";
		
		if ($this->_STAT_API_KEY != "" && $this->_STAT_SUBDOMAIN != "")
		{
			$strURL = "https://" . $this->_STAT_SUBDOMAIN . ".getstat.com/api/v2/" . $this->_STAT_API_KEY . "/";
		}
		
		return($strURL);
	}
	
	/**
	 *	Function : 		GetDataAsJSON
	 *
	 *	Parameters :
	 *
	 *	API_FUNCTION	String	API Function Call to make for example projects/list
	 *
	 *	Returns :
	 *
	 *  JSON Object returned from the API
	 *
	 *	Description : 	
	 *
	 *	Gets the API URL using the API_KEY and SUBDOMAIN
	 *
	 */
	public function GetDataAsJSON($API_FUNCTION)
	{
		$objReturn = null;
		$strURL = $this->GetStatAPIUrl();
		
		if ($strURL != "")
		{
			$strURL .= $API_FUNCTION;
			
			if (strpos($strURL,"?") === false)
			{
				$strURL .= "?format=json";
			}
			else
			{
				$strURL .= "&format=json";
			}
			
			$strReturn = file_get_contents($strURL);
			$objReturn = json_decode($strReturn);
		}
		
		return($objReturn);
	}
	
	/**
	 *	Function : 		GetProjects
	 *
	 *	Returns :
	 *
	 *  Array of StatProject objects
	 *
	 *	Description : 	
	 *
	 *	Gets a full list of projects within the Stat Account
	 *
	 */
	public function GetProjects()
	{
		$arrProjects = array();
		
		if ($this->GetStatAPIUrl() != "")
		{
			$jsonProjectList = $this->GetDataAsJSON("projects/list?results=5000");
			
			foreach($jsonProjectList->Response->Result as $itm)
			{
				$spProject = new StatProject();
				$spProject->ProjectId = $itm->Id;
				$spProject->Name = $itm->Name;
				$spProject->TotalSites = $itm->TotalSites;
				$spProject->CreatedAt = new DateTime($itm->CreatedAt);
				$spProject->UpdatedAt = new DateTime($itm->UpdatedAt);
				$spProject->RequestUrl = $itm->RequestUrl;
				$spProject->_STAT_API_KEY = $this->_STAT_API_KEY;
				$spProject->_STAT_SUBDOMAIN = $this->_STAT_SUBDOMAIN;
				
				array_push($arrProjects,$spProject);
				
			}
		}
		
		return($arrProjects);
	}
	
	/**
	 *	Function : 		GetSites
	 *
	 *	Returns :
	 *
	 *  Array of StatSite objects
	 *
	 *	Description : 	
	 *
	 *	Gets a full list of sites within the Stat Account
	 *
	 */
	public function GetSites()
	{
		$arrSites = array();
		
		if ($this->GetStatAPIUrl() != "")
		{
			$jsonSiteList = $this->GetDataAsJSON("sites/all?results=5000");
			
			foreach($jsonSiteList->Response->Result as $itm)
			{
				$ssSite = new StatSite();
				$ssSite->SiteId = $itm->Id;
				$ssSite->Title = $itm->Title;
				$ssSite->ProjectId = $itm->ProjectId;
				$ssSite->TotalKeywords = $itm->TotalKeywords;
				$ssSite->CreatedAt = new DateTime($itm->CreatedAt);
				$ssSite->UpdatedAt = new DateTime($itm->UpdatedAt);
				$ssSite->RequestUrl = $itm->RequestUrl;
				$ssSite->_STAT_API_KEY = $this->_STAT_API_KEY;
				$ssSite->_STAT_SUBDOMAIN = $this->_STAT_SUBDOMAIN;
				
				array_push($arrSites,$ssSite);
				
			}
		}
		
		return($arrSites);
	}
	
	/**
	 *	Function : 		GetProjectByName
	 *
	 *	Parameters :
	 *
	 *	strName		String		Project Name
	 *
	 *	Returns :
	 *
	 *  Array of StatProject object
	 *
	 *	Description : 	
	 *
	 *	Gets a stat project whose name matches the parameter supplied
	 *
	 */
	function GetProjectByName($strName)
	{
		$pReturn = null;
		
		if ($this->GetStatAPIUrl() != "")
		{
			$arrProjects = $this->GetProjects();
			
			foreach($arrProjects as $spProject)
			{
				if (strcmp(trim($spProject->Name),trim($strName)) == 0)
				{
					$pReturn = $spProject;
					break;
				}
			}
		}
		
		return($pReturn);
	}
	
}

/**
 *
 * 	Class : 		StatProject
 *
 *	Description : 	
 *
 *	Provides a PHP Object versions of a Stat Project
*/
class StatProject
{
	public $ProjectId;
	public $Name;
	public $TotalSites;
	public $CreatedAt;
	public $UpdatedAt;
	public $RequestUrl;
	public $Sites;
	public $_STAT_API_KEY;
	public $_STAT_SUBDOMAIN;
	
	public function __construct()
	{
		$this->Sites = array();
	}
	
	/**
	 *	Function : 		GetSiteByName
	 *
	 *	Parameters :
	 *
	 *	strTitle		String		Site Title
	 *
	 *	Returns :
	 *
	 *  StatSite object
	 *
	 *	Description : 	
	 *
	 *	Gets all of the sites within the project and returns one whose name matches the parameter supplied.
	 *
	 */
	public function GetSiteByName($strTitle)
	{
		$sSite = null;
		
		$this->GetSites();
		
		if (is_array($this->Sites))
		{
			foreach($this->Sites as $ssSite)
			{
				if (strcmp(trim($ssSite->Title),trim($strTitle)) == 0)
				{
					$sSite = $ssSite;
					break;
				}
			}
		}
		
		return($sSite);	
	}
	
	/**
	 *	Function : 		GetSites
	 *
	 *	Returns :
	 *
	 *  None
	 *
	 *	Description : 	
	 *
	 *	Gets all sites belonging to project and adds them to Sites array
	 *
	 */
	public function GetSites()
	{
		$sw = new StatWrapper();
		$sw->_STAT_API_KEY = $this->_STAT_API_KEY;
		$sw->_STAT_SUBDOMAIN = $this->_STAT_SUBDOMAIN;
		
		$this->Sites = array();
		
		if ($this->ProjectId > 0 && $sw->_STAT_API_KEY != "" && $sw->_STAT_SUBDOMAIN)
		{
			$jsonSites = $sw->GetDataAsJSON("sites/list?project_id=" . $this->ProjectId);
			
			foreach($jsonSites->Response->Result as $itm)
			{
				$ssSite = new StatSite();
				$ssSite->SiteId = $itm->Id;
				$ssSite->Title = $itm->Title;
				$ssSite->TotalKeywords = $itm->TotalKeywords;
				$ssSite->CreatedAt = new DateTime($itm->CreatedAt);
				$ssSite->UpdatedAt = new DateTime($itm->UpdatedAt);
				$ssSite->RequestUrl = $itm->RequestUrl;
				$ssSite->_STAT_API_KEY = $this->_STAT_API_KEY;
				$ssSite->_STAT_SUBDOMAIN = $this->_STAT_SUBDOMAIN;
				
				array_push($this->Sites,$ssSite);
			}
			
		}
	}
}

/**
 *
 * 	Class : 		StatSite
 *
 *	Description : 	
 *
 *	Provides a PHP Object versions of a Stat Site
*/
class StatSite
{
	public $SiteId;
	public $ProjectId;
	public $Title;
	public $URL;
	public $TotalKeywords;
	public $CreatedAt;
	public $UpdatedAt;
	public $RequestUrl;
	public $Keywords;
	
	public $_STAT_API_KEY;
	public $_STAT_SUBDOMAIN;
	
	public function __construct()
	{
		$this->Keywords = array();
	}
	
	// Private function to make a keyword object from JSON
	private function CreateKeywordFromJSON($itm)
	{
		$sk = new StatKeyword();
		$sk->KeywordId = $itm->Id;
		$sk->Keyword = $itm->Keyword;
		$sk->Market = $itm->KeywordMarket;
		$sk->Location = $itm->KeywordLocation;
		$sk->Device = $itm->KeywordDevice;
		
		if (isset($itm->KeywordStats->AdvertiserCompetition))
		{
			$sk->AdvertiserCompetition = $itm->KeywordStats->AdvertiserCompetition;
		}
		if (isset($itm->KeywordStats->GlobalSearchVolume))
		{
			$sk->GlobalSearchVolume = $itm->KeywordStats->GlobalSearchVolume;
		}
		if (isset($itm->KeywordStats->RegionalSearchVolume))
		{
			$sk->RegionalSearchVolume = $itm->KeywordStats->RegionalSearchVolume;
		}
		if (isset($itm->KeywordStats->CPC))
		{
			$sk->CPC = $itm->KeywordStats->CPC;
		}
		
		
		$sk->CreatedAt = new DateTime($itm->CreatedAt);
		$sk->UpdatedAt = new DateTime($itm->KeywordRanking->date);
		$sk->RequestUrl = $itm->RequestUrl;
		
		
		if (isset($itm->KeywordRanking->Google->Rank))
		{
			$rnk = new StatRanking();
			$rnk->RankType = "Google Rank";
			$rnk->RankValue = $itm->KeywordRanking->Google->Rank;
			$rnk->RankURL = $itm->KeywordRanking->Google->Url;
			array_push($sk->Rankings,$rnk);
		}
		
		if (isset($itm->KeywordRanking->Google->BaseRank))
		{
			$rnk = new StatRanking();
			$rnk->RankType = "Google Base Rank";
			$rnk->RankValue = $itm->KeywordRanking->Google->BaseRank;
			$rnk->RankURL = $itm->KeywordRanking->Google->Url;
			array_push($sk->Rankings,$rnk);
		}
		
		if (isset($itm->KeywordRanking->Yahoo->Rank))
		{
			$rnk = new StatRanking();
			$rnk->RankType = "Yahoo";
			$rnk->RankValue = $itm->KeywordRanking->Yahoo->Rank;
			$rnk->RankURL = $itm->KeywordRanking->Yahoo->Url;
			array_push($sk->Rankings,$rnk);
		}
		
		if (isset($itm->KeywordRanking->Bing->Rank))
		{
			$rnk = new StatRanking();
			$rnk->RankType = "Bing";
			$rnk->RankValue = $itm->KeywordRanking->Bing->Rank;
			$rnk->RankURL = $itm->KeywordRanking->Bing->Url;
			array_push($sk->Rankings,$rnk);
		}
		
		$sk->_STAT_API_KEY = $this->_STAT_API_KEY;
		$sk->_STAT_SUBDOMAIN = $this->_STAT_SUBDOMAIN;
		
		return($sk);
	}
	
	/**
	 *	Function : 		GetKeywords
	 *
	 * Parameters :
	 *
	 * AllPages		Boolean		Get All Keywords or only the first 500 (page of results)
	 *
	 *	Returns :
	 *
	 *  None
	 *
	 *	Description : 	
	 *
	 *	Gets all of the keywords belonging to a site or just the first page and add to Keywords Array
	 *
	 */
	public function GetKeywords($AllPages)
	{
		$this->Keywords = array();
		$sw = new StatWrapper();
		$sw->_STAT_API_KEY = $this->_STAT_API_KEY;
		$sw->_STAT_SUBDOMAIN = $this->_STAT_SUBDOMAIN;
		
		
		if ($this->SiteId > 0 && $sw->_STAT_API_KEY != "" && $sw->_STAT_SUBDOMAIN)
		{
			$jsonKeywords = $sw->GetDataAsJSON("keywords/list?results=500&site_id=" . $this->SiteId);
			
			foreach($jsonKeywords->Response->Result as $itm)
			{
				$sk = $this->CreateKeywordFromJSON($itm);
				
				array_push($this->Keywords,$sk);
			}
			
			// Lets paginate if we need to
			if ($AllPages)
			{
				$bPage = false;
				$strPageUrl = "";
				
				if (isset($jsonKeywords->Response->nextpage))
				{
					$bPage = true;
					$strPageUrl = $jsonKeywords->Response->nextpage;
					
					// Remove leading slash
					$strPageUrl = substr($strPageUrl,1,strlen($strPageUrl)-1);
					
				}
				
				while ($bPage)
				{
					$jsonKeywords = $sw->GetDataAsJSON($strPageUrl);
				
					foreach($jsonKeywords->Response->Result as $itm)
					{
						$sk = $this->CreateKeywordFromJSON($itm);
						
						array_push($this->Keywords,$sk);
					}
					
					$bPage = false;
					$strPageUrl = "";
					
					if (isset($jsonKeywords->Response->nextpage))
					{
						$bPage = true;
						$strPageUrl = $jsonKeywords->Response->nextpage;
						
						// Remove leading slash
						$strPageUrl = substr($strPageUrl,1,strlen($strPageUrl)-1);
					}
				}
			}
			
		}
	}
	
}

/**
 *
 * 	Class : 		StatKeyword
 *
 *	Description : 	
 *
 *	Provides a PHP Object versions of a Stat Keyword
*/
class StatKeyword
{
	public $KeywordId;
	public $Keyword;
	public $Market;
	public $Location;
	public $Device;
	public $AdvertiserCompetition;
	public $GlobalSearchVolume;
	public $RegionalSearchVolume;
	public $CPC;
	public $CreatedAt;
	public $UpdatedAt;
	public $RequestUrl;
	
	public $Rankings;
	
	public $_STAT_API_KEY;
	public $_STAT_SUBDOMAIN;
	
	public function __construct()
	{
		$this->Rankings = array();
	}
}

/**
 *
 * 	Class : 		StatRanking
 *
 *	Description : 	
 *
 *	Provides a PHP Object versions of a Stat Keyword
*/
class StatRanking
{
	public $RankType;
	public $RankValue;
	public $RankURL;
	
}
?>