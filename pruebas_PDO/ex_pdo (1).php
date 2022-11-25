<?php
try {
	$pdo->beginTransaction();

	$emailUser = IsNullOrEmptyString($user["email"],true);
	//echo $emailUser;
	//and emailwork = '$emailUser'
	$query = "SELECT count(id) as cantusrs, members.* from members where ( (usr_ad = :usr_ad)  );";
	//echo $query;or (email = :email)
	//,								':email' => $user['email'],
	$statement = $pdo->prepare( $query );
	/*
	,
		'emailwork' => $emailUser
	*/
	$statement->execute([
		':usr_ad' => $usr_ad
	]);

	$data = $statement->fetch(PDO::FETCH_ASSOC);
	
	$session_id = session_id();
	$getIp = getIp();
	$isSSL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 1 : 0;
	$isLogout = 0;
	$fk_pageid = $PAGEID;
	$POST = print_r($_REQUEST, true);
	$POST = $POST.'/referer/'.$referer;
	$newIdUser = 'no';
	$usrcompany = $user['company'];
	$usrdivision = $user['division'];

	if ($data){
		if ($data["cantusrs"]>0){
			$idUser = $data["id"];
			$status = (bool)$data["status"];
			
			//aca lo que hago es actualizar mails si ya existen como externo e inhabilito el usuario //18112022

			$query = "
			select 
				count(id) as cantusrs, 
				members.* 
			from members 
				where  (usr_ad is  null)
				and ((  usr = :email)  or ( email = :email2))
				;
			";
			//echo $query;
			$statement = $pdo->prepare( $query );

			$statement->execute([
				 
				':email' => $user['email'],
				':email2' => $user['email']
			]);

			$datausr = $statement->fetch(PDO::FETCH_ASSOC);


			if ($datausr){
				if ($datausr["cantusrs"]>0){
					$idUserUPD = $datausr["id"];
					$statusUPD = (bool)$datausr["status"];
					//if ($statusUPD){
						$queryUPDEmail = "
						update members set 
							updip = :ipaddress,  
							removed = 1,  
							status = 0,  
							dni = CONCAT('_AD_',dni), 
							email = CONCAT('_AD_',email), 
							emailwork = CONCAT('_AD_',emailwork),
							usr = CONCAT( '_AD_',usr)
						WHERE 
							id = :iduser
							;
						";

						$statementUPDEmail = $pdo->prepare( $queryUPDEmail );


						$statementUPDEmail->bindParam(':ipaddress', $getIp,PDO::PARAM_STR );
						$statementUPDEmail->bindParam(':iduser', $idUserUPD,PDO::PARAM_INT);
						//var_dump($pdo->debugDumpParams());
						//var_dump($getIp);

						$statementUPDEmail->execute();
					//}
					
				}
			}
			
			//actualizo los datos que deben quedar ok
			$queryUPDEmail = "
			update members set 
				updip = :ipaddress,  
				usr = :usr, 
				email = :email, 
				emailwork = :emailwork 
			WHERE 
				id = :iduser
				;
			";

			$statementUPDEmail = $pdo->prepare( $queryUPDEmail );

			$statementUPDEmail->bindParam(':usr', $user['email'],PDO::PARAM_STR);
			$statementUPDEmail->bindParam(':email', $user['email'],PDO::PARAM_STR);
			$statementUPDEmail->bindParam(':emailwork', $user['email'],PDO::PARAM_STR);
			$statementUPDEmail->bindParam(':ipaddress', $getIp,PDO::PARAM_STR );
			$statementUPDEmail->bindParam(':iduser', $idUser,PDO::PARAM_INT);
			//var_dump($pdo->debugDumpParams());
			//var_dump($getIp);

			$statementUPDEmail->execute();
			 
			if (!$status){										
				doLogMember(NULL,'usr_inactive_attempt',$idUser);
				$pdo->commit();
				die( '{ "message": "'.$translation->getTranslation("msg_datamismatch").' USUARIO INACTIVO CONTACTE AL SOPORTE", "type" : "error" , "callback" : "","timeout":"" }' );
			}
			
			if (!empty($dni)){
				//valDNIDup($dni,$user['email'],$idUser,1);
				$_SESSION['SU'] = false;
				
				/*verifico si es superuser backend*/
				$querySU = "
				select 
					count(id_user) as cantusrs, 
					cm_users_ad.* 
				from cm_users_ad 
					where  (user_ad = :usr_ad) 
					and superuser = 1
					and active = 1
					;
				";
				//echo $query;
				$statementSU = $pdo->prepare( $querySU );
				/*
				,
					'emailwork' => $emailUser
				*/
				$statementSU->execute([
					':usr_ad' => $usr_ad
				]);

				$dataSU = $statementSU->fetch(PDO::FETCH_ASSOC);
				if ($dataSU){
					if ($dataSU["cantusrs"]>0){
						$_SESSION['SU'] = true;
					}
				}
			}
		}	
	}

	$refererlogin = $_POST[ "refererlogin" ];

	
	if ($user['company']==$user['division']){
		$compDiv = $user['company'];
	}else{
		$compDiv = $user['company'].' '.$user['division'];
	}

	if ( $idUser == 0 ) {
		//si no existe lo ingreso

		$chars = rand();
		$regpass = substr(md5($_SERVER['REMOTE_ADDR'].$chars.microtime().$emailUser.rand(1,100000)),0,8);

		$lang = 2;

		$sqls = "
		INSERT INTO  `members`
		(
		`name`,
		`lastname`,
		`regIP`,
		`fk_pageid`,
		`email`,
		`emailwork`,
		`usr`,
		`dni`,
		`lang`,
		`office`,
		`planta`,
		`pass`,
		`usr_ad`,
		`sessionid`,
		`ipaddress`,
		`request`,
		`referer`,
		`isSSL`,
		`dt`)
		VALUES
		(
		:name,
		:lastname,
		:ipaddress,
		:fk_pageid,
		:email,
		:emailwork,
		:usr,
		:dni,
		:lang,
		:office,
		:planta,
		:regpass,
		:usr_ad,
		:sessionid,
		:ipaddress2,
		:request,
		:referer,
		:isSSL,
		now())
			;";
		//echo $sqls;
		$statement = $pdo->prepare( $sqls );

		$statement->bindParam( ':name', $user['name'] );
		$statement->bindParam( ':lastname', $user['surname'] );
		$statement->bindParam( ':ipaddress', $getIp );
		$statement->bindParam( ':fk_pageid', $fk_pageid,PDO::PARAM_INT );							
		$statement->bindParam( ':email', $emailUser );
		$statement->bindParam( ':emailwork', $emailUser );
		$statement->bindParam( ':usr', $emailUser);
		$statement->bindParam( ':office',$compDiv);
		$statement->bindParam( ':planta',$user['planta']);
		$statement->bindParam( ':dni', $user['dni']);
		$statement->bindParam( ':lang',$lang,PDO::PARAM_INT );
		$statement->bindParam( ':regpass',$regpass );
		$statement->bindParam( ':usr_ad', $user['samaccountname'] );
		$statement->bindParam( ':sessionid', $session_id );
		$statement->bindParam( ':ipaddress2', $getIp );
		$statement->bindParam( ':request', $POST );
		$statement->bindParam( ':referer', $refererlogin);
		$statement->bindParam( ':isSSL', $isSSL,PDO::PARAM_INT );

		$statement->execute();

		$newIdUser = $pdo->lastInsertId();

		//inserto el usuario y lo vuelvo a consultar
		$query = "
		select count(id) as cantusrs,members.* from members where id = :newIdUser;
		";

		$statement = $pdo->prepare( $query );

		//$statement->bindParam( ':dni', $user['dni']);

		$statement->execute([':newIdUser' => $newIdUser]);

		$data = $statement->fetch(PDO::FETCH_ASSOC);
	} 
	if (!empty($newIdUser)){
		$idUser = $data[ "id" ];
	}else{
		$idUser = $newIdUser;
	}

	$emailUser = $data[ "email" ];

	$msg = 'El usuario ya existe como miembro '.$emailUser.' DNI '.$user['dni'];
	
	$hashid = NULL;
	$hashid = isNullOrEmptyString($hashid,true);
	$query = "
	update members set 
		updip = :ipaddress,  
		user_last_login = NOW(), 
		hashid = :hashid
	WHERE 
		id = :iduser
		;
	";

	$statement = $pdo->prepare( $query );

	$statement->bindParam(':hashid', $hashid,PDO::PARAM_STR);
	$statement->bindParam( ':ipaddress', $getIp );
	$statement->bindParam(':iduser', $idUser,PDO::PARAM_INT);

	$statement->execute();
	/*fin registro*/

	doLogMember(1,'login',$idUser);

	$MasterId = $pdo->lastInsertId();

	$pdo->commit();

} catch ( PDOException $exception ) {
	$msg =  '<span class="ui-icon ui-icon-circle-close"></span> PDO error : ' . $exception->getMessage().'  <hr>'.$exception;
	$pdo->rollBack();
	die( '{ "message": "Error de acceso '.$msg.'", "type" : "error" , "callback" : "","timeout":"" }' );
	//die(msg(0,"Datos NO Ingresados ".$msg));

}