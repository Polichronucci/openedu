<?php




$app->post('/mathima', function($request, $response) use ($diy_storage, $diy_restapi){



    global $app;
    $result["controller"] = __FUNCTION__;

	$headers = $request->getHeaders();
	foreach ($headers as $name => $values) {
	    //echo $name . ": " . implode(", ", $values);
	    //$dget[".$name."]=$values;
	}
	//GET
	$allGetVars = $request->getQueryParams();
	foreach($data as $key => $param){
	   //GET parameters list
	}

	//POST or PUT
	$allPostPutVars = $request->getParsedBody();
	foreach($allPostPutVars as $key => $param){
	   //POST or PUT parameters list
	    $dgettmp["$key"]=$param;
	}
   	$datatmp = $allPostPutVars['data'];
	$data = json_decode($datatmp, true);
	foreach($data as $key => $param){
	   //POST or PUT parameters list
	    $dget[$key]=$param;
	}
   $unisxolh = $allPostPutVars['unisxolh'];
   $department = $allPostPutVars['department'];
   $onoma = $allPostPutVars['onoma'];
   $epitheto = $allPostPutVars['epitheto'];
   $email = $allPostPutVars['email'];
   $etmimalession = $allPostPutVars['etmimalession'];
   $lesson = $etmimalession[0]["m"];
   $ellak = $etmimalession[0]["s"];
   $g='';

   $storage = $diy_storage();
   $restapi = $diy_restapi();
   $restapitmp = $restapi['restapi'];
   $restapipoint = $restapi['endpoint'];
   $restapipoint2 = $restapi['endpoint2'];
    try {
 		$g = 'INSERT INTO dataellak ( "onoma", "epitheto", "email", "eidikotita", "ergastirio", "ergastirioonoma", "ergastiriodrastiriotita", "ergastirioperigrafi", "ergastirioypefthinos", "ergastiriourl", "meta", "metatitlos", "idrima", "sxolh" ) VALUES ( :onoma, :epitheto, :email, :eidikotita, :ergastirio, :ergastirioonoma, :ergastiriodrastiriotita, :ergastirioperigrafi, :ergastirioypefthinos, :ergastiriourl, :meta, :metatitlos, :idrima, :sxolh)';
		$tmp = $data['eidikotita'];
        	$stmt = $storage->prepare($g);
		$stmt->bindValue(':onoma', $dget["onoma"]);
		$stmt->bindValue(':epitheto', $dget["epitheto"]);
		$stmt->bindValue(':email', $dget["email"]);
		$stmt->bindValue(':eidikotita', $dget["eidikotita"]);
		$stmt->bindValue(':ergastirio', $dget["ergastirio"]);
		$stmt->bindValue(':ergastirioonoma', $dget["ergastirioonoma"]);
		$stmt->bindValue(':ergastiriodrastiriotita', $dget["ergastiriodrastiriotita"]);
		$stmt->bindValue(':ergastirioperigrafi', $dget["ergastirioperigrafi"]);
		$stmt->bindValue(':ergastirioypefthinos', $dget["ergastirioypefthinos"]);
		$stmt->bindValue(':ergastiriourl', $dget["ergastiriourl"]);
		$stmt->bindValue(':meta', $dget["meta"]);
		$stmt->bindValue(':metatitlos', $dget["metatitlos"]);
		$stmt->bindValue(':idrima', $dget["idrima"]);
		$stmt->bindValue(':sxolh', $dget["sxolh"]);

		$fields['edu_quest_applicant_name']= $dget["onoma"];
		$fields['edu_quest_applicant_surname']= $dget["epitheto"];
		$fields['edu_quest_applicant_email']= $dget["email"];
		$fields['edu_quest_lab_name']=$dget["ergastirioonoma"];
		$fields['edu_quest_lab_activity']= $dget["ergastiriodrastiriotita"];
		$fields['edu_quest_lab_activity_description']= $dget["ergastirioperigrafi"];
		$fields['edu_quest_lab_head']= $dget["ergastirioypefthinos"];
		$fields['edu_quest_lab_website']= $dget["ergastiriourl"];
		$fields['edu_quest_institution']=$dget["idrima"];
		$fields['edu_quest_department']=$dget["sxolh"];

        	$stmt->execute();

        	$lastid = $storage->lastInsertId();
		if($data['eidikotita'] == 'didaktiko' || $data['eidikotita'] == 'fititis' || $data['eidikotita'] == 'ergastiriako'){
			$g1 = 'INSERT INTO datamathima ( "id", "mathima", "ellak", "ellakurl" ) VALUES ( :lastid, :mathima, :ellak, :ellakurl)';
		}elseif($data['eidikotita'] == 'meta'){
                        $g1 = 'INSERT INTO datameta ( "id", "metamathima", "ellak", "ellakurl" ) VALUES ( :lastid, :mathima, :ellak, :ellakurl)';
		}elseif($data['eidikotita'] == 'dioikitiko'){
                        $g1 = 'INSERT INTO datadioikitiko ( "id", "mathima", "ellak", "ellakurl" ) VALUES ( :lastid, :mathima, :ellak, :ellakurl)';
		}
			$arr_length = count($dget["ellak"]);
			$stmt1 = $storage->prepare($g1);
			$ii=1;
			for($i=0;$i<$arr_length;$i++) {
					$stmt1->bindValue(':lastid', $lastid);
					if($data['eidikotita'] == 'dioikitiko'){
						$stmt1->bindValue(':mathima', '');
					}else{
						$stmt1->bindValue(':mathima', $dget["ellak"][$i]["mathima"]);
						$contentellakm = $dget["ellak"][$i]["mathima"];
					}
					if($dget["ellak"][$i]["tech"] != ''){
						$stmt1->bindValue(':ellak', $dget["ellak"][$i]["tech"]);
						$contentellakt = $dget["ellak"][$i]["tech"];
					}
					elseif($dget["ellak"][$i]["url"] != ''){
						$stmt1->bindValue(':ellakurl', $dget["ellak"][$i]["url"]);
						$contentellaku = $dget["ellak"][$i]["url"];
					}
				if($i == $ii){
					$fields['edu_quest_course']= $contentellakm;
					$fields['edu_quest_software']= $contentellakt;
					$fields['edu_quest_software_url']= $contentellaku;
					$TITLOS = $dget["email"];

					$data_json =  '{ "title": "'.$TITLOS.'", "status":"pending" }';
					$exec = 'curl -k --header "Authorization: Basic '.$restapitmp.'" -H "Content-Type: application/json" -X POST  '.$restapipoint.' -d '."'".$data_json."'";

					$exec .= " | jq '.id' 2>&1";
					exec($exec, $output, $return_var);
					$fields1['fields']=$fields;
					$content1 = json_encode($fields1);
					$exec1 = 'curl -k --header "Authorization: Basic '.$restapitmp.'" -H "Content-Type: application/json" -X POST  '.$restapipoint2.'/'.$output[0].' -d '."'".$content1."'";

					$exec1 .= " 2>&1";
					exec($exec1, $output1, $return_var1);
					$contentellakm='';
					$contentellakt='';
					$contentellaku='';
					$output='';
					$stmt1->execute();
					$ii = $ii + 2;
				}
			}
	$content = json_encode($fields);

	//result_messages===============================================================      
	//$data_json =  '{ "title": "Ερωτηματολόγιο για το ανοιχτό λογισμικό", "content": "'.$content.'", "status":"publish" }';
	//$exec = 'curl --header "Authorization: Basic '.$restapitmp.'" -H "Content-Type: application/json" -X post -k  -i '.$restapipoint.' -d '."'".$data_json."'";
	//exec($exec);
        $result["result"]=  $q;
        //$result["dget"]=  $dget;
        //$result["dgettmp"]=  $content1;
        //$result["dgettmp1"]=  $exec;
        //$result["dgettmp2"]=  $output;
        //$result["dgettmp3"]=  $return_var;
        //$result["g"]=  $g;
        $result["status"] = "200";
        $result["message"] = "NoErrors";
    } catch (Exception $e) {
        $result["status"] = $e->getCode();
        $result["message"] = $e->getMessage();
    }

     $response->withHeader('Content-Type', 'application/json;charset=utf-8');
     //$response->getBody()->write(json_encode($result));
     $response->getBody()->write( toGreek( json_encode( $result ) ) );
     //return $response->withJson();

     return $response;
});
