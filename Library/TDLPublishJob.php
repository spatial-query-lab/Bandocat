<?php

//DSpace REST API 5.x (by May 2017)  //See https://wiki.duraspace.org/display/DSDOC5x/REST+API
//to change login information for TDL, locate tdlconfig.ini file and change the username and password
class TDLPublishJob
{

    static protected $ini_dir = "BandoCat_config\\tdlconfig.ini"; //this point to the directory of the TDL configuration file that has TDL REST URL, username (islander email), password
    protected $cookie; //$cookie (authentication cookie) returned to perform POST,PUT,DELETE request
    protected $tdl_email; //store the TDL username/email credential from tdlconfig.ini
    protected $tdl_pwd;  //store the authentication TDL password
    protected $baseUrl; //store the TDL REST URL

    //Constructor:
    //Read the file, set email, password, base URL
    // Login (TDL_LOGIN) if needed
    // Set cookie
    function __construct($iToken = null)
    {
        $root = substr(getcwd(),0,strpos(getcwd(),"htdocs\\")); //point to xampp// directory
        $config = parse_ini_file($root . TDLPublishJob::$ini_dir);
        $this->tdl_email = $config['TDLemail'];
        $this->tdl_pwd = $config['TDLpwd'];
        $this->baseUrl = $config['baseURL'];
        if($iToken == null)
            $this->TDL_LOGIN();
        else $this->cookie = $iToken;
    }

    /**
     * @return null
     */
    public function getToken()
    {
        return $this->cookie;
    }
    /**
     * @param null $cookie
     */
    public function setToken($cookie)
    {
        $this->cookie = $cookie;
    }
    //check_status
    //method: GET
    //return: status (json)
    /**********************************************
     * Function: TDL_CHECK_STATUS
     * Description: TEST REST API connection
     * Parameter(s): None
     * Return value(s): return a header from the TDL server
     ***********************************************/
    public function TDL_CHECK_STATUS()
    {
        $hdr_arr = array("rest-dspace-cookie:" . $this->cookie,'Content-Type: application/json','Accept: application/json');
        $ch = curl_init($this->baseUrl . "status");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER,$hdr_arr);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //disable SSL
        //curl_setopt($ch,CURLOPT_HEADER,true); //return header
        $output =  curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    //get_communities, if $communityID = null, return all communities
    //method: GET
    //return: list of all communities (json)
    /**********************************************
     * Function: TDL_GET_COMMUNITIES
     * Description: Get Community (communities) metadata from TDL
     * Parameter(s): $communityID (int - Default : NULL) - if no communityID = null -> return all communities if $communityID = null. otherwise, return the $communityID only
     * Return value(s): return metadata about community(s)
     ***********************************************/
    public function TDL_GET_COMMUNITIES($communityID = null)
    {
        if($communityID == null)
            $ch = curl_init($this->baseUrl . "communities");
        else $ch = curl_init($this->baseUrl . "communities/" . $communityID);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //disable SSL
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    //get_collections, if $communityID = null, get all collections, else get an array of collections of the specified community.
    //method: GET
    //return: list of all collections (json)
    /**********************************************
     * Function: TDL_GET_COLLECTIONS
     * Description: Get Collection(s) (ALL collections in TDL OR Collections in 1 community) metadata from TDL
     * Parameter(s): $communityID (int - Default : NULL) - if no communityID, return all collections in TDL
     * Return value(s): return information about collection(s)
     ***********************************************/
    public function TDL_GET_COLLECTIONS($communityID = null)
    {
        if($communityID == null)
            $ch = curl_init($this->baseUrl . "collections");
        else $ch = curl_init($this->baseUrl . "communities/" . $communityID ."/collections");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //disable SSL
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
/*DEPRECIATED
    //login
    //method: POST
    //desc: given email and password, login and retrieve dspace-rest-cookie ($cookie)
    **********************************************
     * Function: TDL_LOGIN
     * Description: login to TDL's Dspace, store the cookie value from the returned header
     * Parameter(s): None
     * Return value(s): None
     ***********************************************
     public function TDL_LOGIN($cert = null)
    {
        $ch = curl_init();
        $data = array("email" => $this->tdl_email, "password" => $this->tdl_pwd);
        $data_str = json_encode($data);
        $options = array(CURLOPT_URL => $this->baseUrl . "login",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data_str,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIESESSION => true
        );
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_str),

        ));

        // $output contains the output string
        if($cert != null)
        {
            $certURL = getcwd() . "\\" . "USERTrustRSACertificationAuthority.crt";
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,true);
            curl_setopt($ch,CURLOPT_CAINFO, $certURL);
            //curl_setopt($ch,CURLOPT_CAPATH, $certURL);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
        }
        else {

            curl_setopt($ch, CURLOPT_HEADER, false); //do not return header
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //disable SSL
        }
		$information = curl_getinfo($ch);
		print_r($information);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch);
        $this->cookie = curl_exec($ch);
		 $info = curl_getinfo($ch);

        // close curl resource to free up system resources
        curl_close($ch);
    }



    //custom GET request from Dspace REST API 5.x
    //See https://wiki.duraspace.org/display/DSDOC5x/REST+API
    //method: GET
    //@Param: $str : URI
    //return: GET output

    **********************************************
     * Function: TDL_CUSTOM_GET
     * Description: custom GET request for TDL REST API
     * Parameter(s): $str (string) - appended REST endpoint. For example: "collections/1/items/" to return all items in collection 1
     * Return value(s): return a HTTP header
     ***********************************************
    public function TDL_CUSTOM_GET($str)
    {
        $ch = curl_init($this->baseUrl . $str);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //disable SSL
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    //custom DELETE request from Dspace REST API 5.x
    //See https://wiki.duraspace.org/display/DSDOC5x/REST+API
    //method: DELETE
    //@Param: $str : URI
    //return: http code (200 = OK)
    **********************************************
     * Function: TDL_CUSTOM_DELETE
     * Description: custom DELETE request for TDL REST API
     * Parameter(s): $str (string) - appended REST endpoint. For example: "items/5662" to delete item ID 5662
     *               $certURL (str - Default: null) - location of CA certificate file (for SSL), null = SSL disabled
     * Return value(s): Info code: "200" means GOOD, "500" = internal server error, "404" not found ,....
     ***********************************************
    public function TDL_CUSTOM_DELETE($str,$certURL = null)
    {
        $header = array('rest-dspace-cookie:' . $this->cookie,'Accept:application/json');
        $ch = curl_init($this->baseUrl . $str);

        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
        //if $cert_url is blank => disable ssl; otherwise, setup SSL certficate
        if($certURL != null)
        {
            //certificate
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
            curl_setopt($ch,CURLOPT_CAINFO, $certURL);
            curl_setopt($ch,CURLOPT_CAPATH, $certURL);
        }
        else curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $info['http_code'];
    }

    **********************************************
     * Function: TDL_CUSTOM_PUT
     * Description: custom PUT request for TDL REST API to update item
     * Parameter(s): $str (string) - appended REST endpoint. For example: "items/5662" to delete item ID 5662
     *               $filename (string) - name of the file that store new information (usually a JSON)
     *               $mime (string) - mime type of the $filename (usually application/json)
     *               $certURL (str - Default: null) - location of CA certificate file (for SSL), null = SSL disabled
     * Return value(s): return header if header code is "200" , otherwise false
     ***********************************************
    function TDL_CUSTOM_PUT($str,$filename,$mime,$certURL = null)
    {
        $data = file_get_contents($filename); //read file into variables (json/xml)
        //prepare header
        $header = array('rest-dspace-cookie:' . $this->cookie,'Content-Type:' . $mime,'Accept:' . $mime );

        $ch = curl_init($this->baseUrl . $str);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_HEADER,true);
        //if $cert_url is blank => disable ssl; otherwise, setup SSL certficate
        if($certURL != null)
        {
            //certificate
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
            curl_setopt($ch,CURLOPT_CAINFO, $certURL);
            curl_setopt($ch,CURLOPT_CAPATH, $certURL);
        }
        else curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // if any redirection after upload

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if($info['http_code'] == "200") //if http_code is 200, extract the item id from the header
            return $result;
        else
        {
            //echo $info['http_code'] . "\n";
            //print_r($info);
            return false;
        }
    }

    **********************************************
     * Function: TDL_CUSTOM_POST
     * Description: custom POST request for TDL REST API to publish item
     * Parameter(s): $str (string) - appended REST endpoint. For example: "collections/3" to post new item to collection 3
     *               $filename (string) - name of the file that store new information (usually a JSON)
     *               $mime (string) - mime type of the $filename (usually application/json)
     *               $certURL (str - Default: null) - location of CA certificate file (for SSL), null = SSL disabled
     * Return value(s): return header if header code is "200" , otherwise false
     ***********************************************
    function TDL_CUSTOM_POST($str,$filename,$mime,$certURL = null)
    {
        $data = file_get_contents($filename); //read file into variables (json/xml)
        //prepare header
        $header = array('rest-dspace-cookie:' . $this->cookie,'Content-Type:' . $mime,'Accept:' . $mime );

        $ch = curl_init($this->baseUrl . $str);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_HEADER,true);
        //if $cert_url is blank => disable ssl; otherwise, setup SSL certficate
        if($certURL != null)
        {
            //certificate
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
            curl_setopt($ch,CURLOPT_CAINFO, $certURL);
            curl_setopt($ch,CURLOPT_CAPATH, $certURL);
        }
        else curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // if any redirection after upload

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);


        if($info['http_code'] == "200") //if http_code is 200, extract the item id from the header
            return $result;
        else
        {
            //echo $info['http_code'] . "\n";
            return false;
        }
    }




    **********************************************
     * Function: TDL_GET_BITSTREAMS
     * Description: Get Bitstreams' metadata from an item in TDL given the item ID
     * Parameter(s): $itemID (int) - item id in Dspace, stored in `document` table (dspaceID)
     * Return value(s): return an array of bitstreams (metadata of bitstreams). For example: Md5 checksum, name, bitstreamID
     ***********************************************
    public function TDL_GET_BITSTREAMS($itemID)
    {
        //var_dump($this->baseUrl . "items/" . $itemID . "/bitstreams");
        $ch = curl_init($this->baseUrl . "items/" . $itemID . "/bitstreams");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //disable SSL
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }







    //get_items
    //method: GET
    //@param: $collection_id
    //return: all items if $collectionid is blank OR all items in a specified collection
    **********************************************
     * Function: TDL_GET_ITEMS
     * Description: Get Item(s) (ALL items in TDL OR Items in 1 collection) metadata from TDL
     * Parameter(s): $tdlCollectionID (int - Default : NULL) - if no $tdlCollectionID, return all items in TDL
     * Return value(s): return information about item(s)
     ***********************************************
    public function TDL_GET_ITEMS($tdlCollectionID = null)
    {
        if($tdlCollectionID != null)
            $ch = curl_init($this->baseUrl . "collections/" . $tdlCollectionID . "/items");
        else $ch = curl_init($this->baseUrl . "items");

        curl_setopt($ch,CURLOPT_HTTPHEADER,array("rest-dspace-cookie:" . $this->cookie,'Content-Type:application/json'));
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //disable SSL
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    //post_new_items
    //method: POST
    //@param: $collectionid, $filename (json/xml), $mime (mime type),
    // 		  $cert_url (certification .crt file path in config.php OR leave it blank)
    //return value: if the POST is good (http_code == 200) => return the handle uri (include item_id)
    //				else return -1

    **********************************************
     * Function: TDL_POST_NEW_ITEM
     * Description: POST a new ITEM to TDL
     * Parameter(s): $tdlCollectionID (int) - TDL collection (ID) where the new item will be posted to
     *               $fileName (string) - name of the file that store new information (usually a JSON)
     *               $mime (string) - mime type of the $filename (usually application/json)
     *               $certURL (str - Default: null) - location of CA certificate file (for SSL), null = SSL disabled
     * Return value(s): return a header if httpcode = 200, otherwise, false
     ***********************************************
    public function TDL_POST_NEW_ITEM($tdlCollectionID,$fileName,$mime,$certURL = null)
    {
        $data = file_get_contents($fileName); //read file into variables (json/xml)
        //prepare header
        $header = array('rest-dspace-cookie:' . $this->cookie,'Content-Type:' . $mime,'Accept:' . $mime );

        $ch = curl_init($this->baseUrl . "collections/" . $tdlCollectionID . "/items");
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_HEADER,true);

        //if $cert_url is blank => disable ssl; otherwise, setup SSL certficate
        if($certURL != null)
        {
            //certificate
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
            curl_setopt($ch,CURLOPT_CAINFO, $certURL);
            curl_setopt($ch,CURLOPT_CAPATH, $certURL);
        }
        else curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // if any redirection after upload

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        // print_r($result);
        // print_r($info);
        if($info['http_code'] == "200") //if http_code is 200, extract the item id from the header
            return $result;
        else
        {
            //echo $info['http_code'] . "\n";
            return false;
        }
    }




       //delete_item
      //method: DELETE
      //@param: $itemid
      // 		  $cert_url (certification .crt file path in config.php OR leave it blank)
      //return value: return the http_code
      **********************************************
       * Function: TDL_DELETE_ITEM
       * Description: DELETE an item in TDL
       * Parameter(s): $itemID (int) - TDL item ID of the item to be deleted
       *               $certURL (str - Default: null) - location of CA certificate file (for SSL), null = SSL disabled
       * Return value(s): return a http code from the header
       **********************************************
      public function TDL_DELETE_ITEM($itemID,$certUrl = null)
      {
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,$this->baseUrl . "items/" . $itemID);
          curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
          $header = array('cookie: JSESSIONID=' . $this->cookie, 'Accept:' . $mime );
          curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
          curl_setopt($ch,CURLOPT_HEADER,true);
          curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");


         // $header = array('rest-dspace-cookie:' . $this->cookie,'Accept:application/json');
        //  $ch = curl_init($this->baseUrl . "items/" . $itemID);

         // curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
         // curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        //  curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
          //if $cert_url is blank => disable ssl; otherwise, setup SSL certficate
           if($certUrl != null)
          {
              //certificate
              curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
              curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
              curl_setopt($ch,CURLOPT_CAINFO, $certUrl);
              curl_setopt($ch,CURLOPT_CAPATH, $certUrl);
          }
          else curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

          $result = curl_exec($ch);
          $info = curl_getinfo($ch);
          curl_close($ch);
          return $info['http_code'];
      }
*/

    /******************************************************
     * Sam Master work
     ******************************************************

    /**********************************************
     * Function: TDL_POST_BITSTREAM
     * Description: POST a new Bitstream to an item in TDL
     * Parameter(s): $itemID (int) - TDL item (ID) where the new file will be attached to
     *               $name (string) - filename (the original in BandoCat server)
     *               $postName (string) - Name of the file in TDL server
     *               $certURL (str - Default: null) - location of CA certificate file (for SSL), null = SSL disabled
     * Return value(s): return a http code from the header
     ***********************************************/
    public function TDL_POST_BITSTREAM($itemID,$path,$file)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->baseUrl . "items/" . $itemID . "/bitstreams?name=" . $file);
        curl_setopt($ch,CURLOPT_POST,true);
        $header = array('cookie: JSESSIONID=' . $this->cookie,'Content-Type:application/json', 'Accept:application/json');
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $data = file_get_contents($path);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,true);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
        return $info;

    }

    /**********************************************
     * Function: TDL_PUT_ITEM_METADATA
     * Description: Specific PUT request for an ITEMS metadata
     * Parameter(s): $dspaceID (string)
     * Return value(s): return a HTTP header
     ***********************************************/
    public function TDL_PUT_ITEM_METADATA($dspaceID,$jsonfilename)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->baseUrl . "items/" . $dspaceID ."/metadata");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        $header = array('cookie: JSESSIONID=' . $this->cookie, 'Accept: application/json' );
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $data = file_get_contents($jsonfilename);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,true);



        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        print_r($info);
        curl_close($ch);
        return $info;

    }
	 /**********************************************
     * Function: TDL_GET_ITEM_METADATA
     * Description: Specific GET request for an ITEMS metadata
     * Parameter(s): $dspaceID (string)
     * Return value(s): return a HTTP header
     ***********************************************/
    public function TDL_GET_ITEM_METADATA($dspaceID)
    {
        $ch = curl_init($this->baseUrl . "items/" . $dspaceID . "/metadata");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
    /**********************************************
     * Function: TDL_DELETE_ITEM
     * Description: DELETE an item in TDL
     * Parameter(s): $itemID (int) - TDL item ID of the item to be deleted
     *               $certURL (str - Default: null) - location of CA certificate file (for SSL), null = SSL disabled
     * Return value(s): return a http code from the header
     ***********************************************/
    public function TDL_DELETE_ITEM($itemID,$mime,$certUrl = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->baseUrl . "items/" . $itemID);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
        $header = array('cookie: JSESSIONID=' . $this->cookie,'Content-Type:' . $mime, 'Accept: ' . $mime );
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,true);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");



        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $info['http_code'];
    }


    /**********************************************
     * Function: TDL_GET_ITEM_BITSTREAMS
     * Description: Specific GET request for TDL REST API
     * Parameter(s): $dspaceID (string)
     * Return value(s): return a HTTP header
     ***********************************************/
    public function TDL_GET_ITEM_BITSTREAMS($dspaceID)
    {
        $ch = curl_init($this->baseUrl . "items/" . $dspaceID . "/bitstreams");
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //disable SSL
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
		//json_decode($output)
    }

    /**********************************************
     * Function: TDL_POST_NEW_ITEM
     * Description: POST a new ITEM to TDL
     * Parameter(s): $tdlCollectionID (int) - TDL collection (ID) where the new item will be posted to
     *               $fileName (string) - name of the file that store new information (usually a JSON)
     *               $mime (string) - mime type of the $filename (usually application/json)
     *               $certURL (str - Default: null) - location of CA certificate file (for SSL), null = SSL disabled
     * Return value(s): return a header if httpcode = 200, otherwise, false
     ***********************************************/
    public function TDL_POST_ITEM($tdlCollectionID,$fileName,$mime,$certURL = null)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->baseUrl . "collections/" . $tdlCollectionID . "/items");
        curl_setopt($ch,CURLOPT_POST,true);
        $header = array('cookie: JSESSIONID=' . $this->cookie,'Content-Type:' . $mime, 'Accept: ' . $mime );
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $data = file_get_contents($fileName); //get file to be published
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);




        $result = curl_exec($ch);
        $info = curl_getinfo($ch);

        curl_close($ch);
        if($info['http_code'] == "200") //if http_code is 200, extract the item id from the header
            return $result;
        else
        {
            return false;
        }
    }
    /**********************************************
     * Function: TDL_DELETE_BITSTREAM
     * Description: DELETE a Bitstream to an item in TDL
     * Parameter(s): $bitstreamID (int) - Bitstream ID of the bitstream to be deleted
     * Return value(s): return a http code from the header
     ***********************************************/
    public function TDL_DELETE_BITSTREAM($bitstreamID,$mime,$certUrl = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->baseUrl . "bitstreams/" . $bitstreamID);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
        $header = array('cookie: JSESSIONID=' . $this->cookie, 'Accept:' . $mime );
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,true);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $info['http_code'];
    }



    /**********************************************
     * Function: TDL_PUT_BITSTREAM
     * Description: UPDATE a Bitstream in TDL
     * * Parameter(s): $itemID (int) - TDL item (ID) where the new file will be attached to
     *                 $bitID (string) - TDL bitstream ID
     *                 $file  (string) - File Path to the JPG that will update TDL
     *				   $mime  (string) - Required String for TDL connection
     * Return value(s): return a http code from the header
     ***********************************************/
    public function TDL_PUT_BITSTREAM($itemID,$bitID,$file,$mime,$certUrl = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$this->baseUrl . "bitstreams/" . $bitID ."/data?format=TEST");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        $header = array('cookie: JSESSIONID=' . $this->cookie, 'Accept:' . $mime );
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        $data = file_get_contents($file);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER,true);



        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        print_r($info);
        curl_close($ch);
        return $info;
    }

    /********************************************************************
     * Function: TDL_LOGIN
     * Description: Generates a CURL to login to the TDL website
     *  Parameter(s): None
     * Return value(s): returns a JSESSIONID (cookie) that is
     * 				    stored in a variable for use in future TDL calls
     **********************************************************************/
    public function TDL_LOGIN($cert = null)
    {
        $ch = curl_init();
        $email = $this->tdl_email;
        $password = $this->tdl_pwd;
        $datastring = "email=". $email . "&password=".$password;
        curl_setopt($ch, CURLOPT_URL,$this->baseUrl . "login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datastring);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $result = curl_exec($ch);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }


        $this->cookie = $cookies["JSESSIONID"];

        curl_close($ch);
    }

}