<?php

namespace Digidez;

use Exception;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Drive;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Digidez\Functions;

class Google_Calendar{
	var $mode = 'dev';
	var $calendarIds = [
		'boho' => '', // boho_calendar_id
		'gold' => '', // gold_calendar_id
		'black' => '', // black_calendar_id
	];
	var $google_api_key = '';

	function __construct(){
		$google           = get_field('google', 'option');
		#Functions::_debug($google);
		$this->mode       = $google['mode'];
		$this->calendarIds['boho'] = $google['boho_calendar_id'];
		$this->calendarIds['gold'] = $google['gold_calendar_id'];
		$this->calendarIds['black'] = $google['black_calendar_id'];
		$this->google_api_key = $google['map_api_key'];
	}

	private function getClient3(){
		$client = new Google_Client();
		//The json file you got after creating the service account
		putenv('GOOGLE_APPLICATION_CREDENTIALS='.CONFIG_DIR.DIRECTORY_SEPARATOR.$this->mode.DIRECTORY_SEPARATOR.'Bride-Stories-324b58800735.json');
		$client->useApplicationDefaultCredentials();
		$client->setApplicationName("Bride Stories");
		$client->setScopes(Google_Service_Calendar::CALENDAR);
		$client->setAccessType('offline');

		return ['error' => 0, 'message' => '', 'client' => $client];
	}

	private function getClient2(){
		$client_id = '260244516435-7jmatnbnqt7r1tu7msvgehog6pea7erb.apps.googleusercontent.com';
		$service_account_name = 'bridestoriesservice@bride-stories-1570106892440.iam.gserviceaccount.com';
		$key_file_location = CONFIG_DIR.DIRECTORY_SEPARATOR.$this->mode.DIRECTORY_SEPARATOR.'Bride-Stories-60876f4e3798.p12';

		if (!strlen($service_account_name) || !strlen($key_file_location)){
			echo 'missingServiceAccountDetailsWarning';
		}


		$client = new Google_Client();
		$client->setApplicationName("Bride Stories");
		if (isset($_SESSION['service_token'])) {
			$client->setAccessToken($_SESSION['service_token']);
		}

		$key = file_get_contents($key_file_location);
		$client->setAuthConfig(array(
			'type' => 'service_account',
			'client_email' => 'bridestoriesrostock@gmail.com',
			'client_id' => $client_id,
			'private_key' => $key
		));

		$_SESSION['service_token'] = $client->getAccessToken();

		return ['error' => 0, 'message' => '', 'client' => $client];
	}

	private function getClient(){
		$client = new Google_Client();
		$client->setApplicationName('Google Calendar API PHP Quickstart');
		$client->setScopes(array(Google_Service_Calendar::CALENDAR, Google_Service_Drive::DRIVE));
		$client->setAuthConfig(CONFIG_DIR.DIRECTORY_SEPARATOR.$this->mode.DIRECTORY_SEPARATOR.'credentials.json');
		$client->setDeveloperKey($this->google_api_key);
		$client->setAccessType('offline');
		//$client->setRedirectUri('http://'.$_SERVER['HTTP_HOST'].'/oauth2callback.php');
		$client->setIncludeGrantedScopes(true);
		//$client->setPrompt('select_account consent');

		// Load previously authorized token from a file, if it exists.
		// The file token.json stores the user's access and refresh tokens, and is
		// created automatically when the authorization flow completes for the first
		// time.

		$tokenPath = CONFIG_DIR.DIRECTORY_SEPARATOR.$this->mode.DIRECTORY_SEPARATOR.'token.json';
		if(file_exists($tokenPath)){
			$accessToken = json_decode(file_get_contents($tokenPath), true);
			$client->setAccessToken($accessToken);
		}

		// If there is no previous token or it's expired.
		if($client->isAccessTokenExpired()){
			// Refresh the token if possible, else fetch a new one.
			if($client->getRefreshToken()){
				$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			}else{
				// Request authorization from the user.
				//$authUrl = $client->createAuthUrl();
				//printf("Open the following link in your browser:\n%s\n", $authUrl);
				//print 'Enter verification code: ';
				//$authCode = trim(fgets(STDIN));

				// Exchange authorization code for an access token.
				$accessToken = $client->fetchAccessTokenWithAuthCode('4/rwGlXXmOownzbtlhpCKJmkDlWqXuWnOf64bgB3sxJ2sndLQBTSv22W4llbVmwll7a8lELbAozbNKN_gEOBZ-BMM');
				$client->setAccessToken($accessToken);

				// Check to see if there was an error.
				if(array_key_exists('error', $accessToken)){
					return ['error' => 1, 'message' => implode(', ', $accessToken), 'client' => null];
					//throw new Exception(implode(', ', $accessToken));
				}
			}
			// Save the token to a file.
			if(!file_exists(dirname($tokenPath))){
				mkdir(dirname($tokenPath), 0700, true);
			}
			file_put_contents($tokenPath, json_encode($client->getAccessToken()));
		}

		return ['error' => 0, 'message' => '', 'client' => $client];
		//return $client;
	}

	public function getEvents($suiten_name = 'all'){
		$ret = ['status' => 200, 'message' => 'OK', 'results' => []];

		$_events = [];

		// Get the API client and construct the service object.
		$client_data = $this->getClient();

		if($client_data['error']){
			$ret['status'] = 400;
			$ret['message'] = $client_data['message'];
		}else{
			$client  = $client_data['client'];
			$service = new Google_Service_Calendar($client);

			// Print the next 10 events on the user's calendar.
			$optParams = array(
				'maxResults'   => 1000,
				'orderBy'      => 'startTime',
				'singleEvents' => true,
				'timeMin'      => date('c', mktime(9,0,0)),
				//'timeMin'      => date('c'),
			);

			$cal_key = 'all';
			$calendarId = 'all';

			switch($suiten_name){
				case "GOLD SUITE":
					$cal_key = 'gold';
					break;
				case "BOHO SUITE":
					$cal_key = 'boho';
					break;
				case "BLACK SUITE":
					$cal_key = 'black';
					break;
				default:
					break;
			}

			if($cal_key != 'all'){
				$calendarId = $this->calendarIds[$cal_key];
			}else{
				$calendarId = $cal_key;
			}

			#Functions::_debug($calendarId);

			if($calendarId == 'all'){
				foreach($this->calendarIds as $k => $calendarId){
					$results     = $service->events->listEvents($calendarId, $optParams);
					$_events[$k] = $results->getItems();
				}
			}else{
				$results   = $service->events->listEvents($calendarId, $optParams);
				$_events[$cal_key] = $results->getItems();
			}
			#Functions::_debug($_events);

			if(empty($_events)){
				$ret['status'] = 300;
				$ret['message'] = "No upcoming events found.";
			}else{
				$u = [];
				foreach($_events as $cal_key => $events){
					foreach($events as $i => $event){
						$cal_name = $event->organizer->displayName;
						$start = $event->start->dateTime;
						$end   = $event->end->dateTime;
						if(empty($start)){
							$start = $event->start->date;
							$end   = $event->end->date;
						}
						$sd = Carbon::parse($start)->locale('de');
						$ed = Carbon::parse($end)->locale('de');

						$tmp = [
							'organizer' => $cal_name,
							'name'       => $event->getSummary(),
							'start'      => $start,
							'start_date' => $sd->format('d.m.Y'),
							'start_time' => $sd->format('H:i'),
							'end'        => $end,
							'end_date'   => $ed->format('d.m.Y'),
							'end_time'   => $ed->format('H:i'),
						];
						/*
						$ret['results'][$i]['name']       = $event->getSummary();
						$ret['results'][$i]['start']      = $start;
						$ret['results'][$i]['start_date'] = $sd->format('d.m.');
						$ret['results'][$i]['start_time'] = $sd->format('H:i');
						$ret['results'][$i]['end']        = $end;
						$ret['results'][$i]['end_date']   = $ed->format('d.m.');
						$ret['results'][$i]['end_time']   = $ed->format('H:i');
						*/
						$key     = md5($cal_name.'|'.$start.'|'.$end);
						$u[$key] = $tmp;
					}
				}
				$ret['results'] = $u;
			}
		}

		#Functions::_debug($ret);

		return $ret;
	}

	public function setEvent($suiten_id, $event_data = []){
		$ret = ['id' => 0, 'link' => ''];

		if(!empty($suiten_id) && !empty($event_data)){
			$client_data = $this->getClient();
			$client      = $client_data['client'];
			$service     = new Google_Service_Calendar($client);
			$event       = new Google_Service_Calendar_Event($event_data);

			$calendarId = '';

			switch($suiten_id){
				case "GOLD SUITE":
					$calendarId = $this->calendarIds['gold'];
					break;
				case "BOHO SUITE":
					$calendarId = $this->calendarIds['boho'];
					break;
				case "BLACK SUITE":
					$calendarId = $this->calendarIds['black'];
					break;
			}

			if(!empty($calendarId)){
				//$calendarId .= 'F'; // For special aborting process
				$event       = $service->events->insert($calendarId, $event);
				$ret['id']   = $event->getId();
				$ret['link'] = $event->htmlLink;
			}
		}

		return $ret;
	}

	public function getWorkCalendar($suiten_name = 'all', $direction = 'next', $start_date = 'now', $limit = 6, $include_months = true){
		$dates = [];
		$available_times = [];

		$start_time = 11;
		$end_time = 19;
		$interval = 2;

		for($i = $start_time; $i < $end_time; $i += $interval){
			$j = $i.':00-'.($i+$interval).':00';
			$available_times[$j] = 'available';
		}

		Carbon::setLocale('Europe/Berlin');
		CarbonPeriod::setLocale('Europe/Berlin');

		if($start_date == 'now'){
			$begin = Carbon::now('Europe/Berlin');
			$end = Carbon::now('Europe/Berlin');
		}else{
			$begin = Carbon::create($start_date, 'Europe/Berlin');
			$end = Carbon::create($start_date, 'Europe/Berlin');
		}
		$end = $end->modify('+10 day');

		$exclude_date = 0;
		if($start_date == 'now'){
			//$end = $end->modify('+10 day');
			$period = CarbonPeriod::create($begin, $end);
		}else{
			switch($direction){
				case "next":
					$exclude_date = CarbonPeriod::EXCLUDE_START_DATE;
					//$end = $end->modify('+10 day');
					break;
				case "prev":
					$exclude_date = CarbonPeriod::EXCLUDE_END_DATE;
					//$begin = $begin->modify('-10 day');
					break;
			}
			$period = CarbonPeriod::create($begin, $end, $exclude_date);
		}
		#Functions::_debug($period);

		/*$begin = new \DateTime($start_date);
		$end = new \DateTime($start_date);
		$end = $end->modify( '+6 day' );

		$interval = new \DateInterval('P1D');
		$daterange = new \DatePeriod($begin, $interval ,$end);
		Functions::_debug($daterange);*/

		foreach($period as $key => $date){
			$d = $date->format('d.m.Y');
			#Functions::_debug($d);
			$wd = $date->weekDay();
			if($wd > 1){
				if(count($dates) < $limit){
					$dates[$d] = [
						'id' => 'id'.str_replace('.', 'w', $d),
						'date' => $date->format('Y-m-d'),
						'weekday' => $wd,
						'weekname' => $date->locale('de')->dayName,
						'times' => $available_times,
						'available_times' => count($available_times)
					];
				}
			}
		}
		#Functions::_debug($dates);

		$events = $this->getEvents($suiten_name);
		#Functions::_debug($events);

		if($events['status'] == 200){
			if(!empty($events['results']) && is_array($events['results'])){
				foreach($events['results'] as $k => $result){
					//$event_start_time = intval(str_replace(':', '', $result['start_time']));
					//$event_end_time = intval(str_replace(':', '', $result['end_time']));

					$event_start_date = Carbon::create($result['start_date'].'T'.$result['start_time']);
					$event_end_date = Carbon::create($result['end_date'].'T'.$result['end_time']);
					$event_period = CarbonPeriod::create($event_start_date, $event_end_date);

					#Functions::_debug([$result['start_date'], $event_start_time, $event_end_time]);
					if(isset($dates[$result['start_date']])){
						foreach($dates[$result['start_date']]['times'] as $times_interval => $status){
							$a = explode('-', $times_interval);
							$start_time = intval(str_replace(':', '', $a[0]));
							$end_time = intval(str_replace(':', '', $a[1]));

							$start_date = Carbon::create($dates[$result['start_date']]['date'].'T'.$start_time);
							$end_date = Carbon::create($dates[$result['end_date']]['date'].'T'.$end_time);
							$cal_date_period = CarbonPeriod::create($start_date, $end_date);

							if($event_period->overlaps($cal_date_period)){
								$dates[$result['start_date']]['times'][$times_interval] = 'reserved';
							}
							#Functions::_debug([$start_time, $end_time]);
							/*if(
								($start_time > $event_start_time && $end_time < $event_end_time)
								||
								(($start_time > $event_start_time && $start_time < $event_end_time) || ($end_time > $event_start_time && $end_time < $event_end_time))
								||
								($start_time == $event_start_time || $end_time == $event_end_time)
								||
								($event_start_time > $start_time && $event_end_time < $end_time)
							){
								$dates[$result['start_date']]['times'][$times_interval] = 'reserved';
							}*/
							/*if(
								($event_start_time >= $start_time && $event_start_time < $end_time)
								||
								($event_end_time > $start_time && $event_end_time <= $end_time)
							){
								//$dates[$result['start_date']]['times'][$times_interval] = 'reserved';
							}*/
						}
						$c = array_count_values($dates[$result['start_date']]['times']);
						$dates[$result['start_date']]['available_times'] = isset($c['available']) ? $c['available'] : 0;

						/*$j = $result['start_time'].'-'.$result['end_time'];
						$dates[$result['start_date']]['times'][$j] = 'reserved';
						$dates[$result['start_date']]['available_times'] -= 1;*/
					}
				}
			}
		}

		#Functions::_debug($limit);
		/*if(!empty($dates)){
			$_dates = $dates;
			$dates = [];
			$i = 0;
			foreach($_dates as $k => $date){
				if($i >= $offset && $i < $offset+$limit){
					$dates[$k] = $date;
				}
				$i++;
			}
		}*/

		#Functions::_debug($dates);
		reset($dates);
		$first_item = current($dates);
		$last_item = end($dates);

		$first_date = $first_item['date'];
		$last_date = $last_item['date'];

		#Functions::_debug($first_date);
		$carbon_first_date = Carbon::create($first_date);
		$tmp = [];
		while(true){
			$carbon_first_date->subDay();
			if($carbon_first_date->endOfDay()->timestamp < Carbon::now()->endOfDay()->timestamp){
				break;
			}
			if($carbon_first_date->weekday() > 1){
				$tmp[] = $carbon_first_date->toDateString();
			}
			if(count($tmp) >= $limit){
				break;
			}
		}

		if(empty($tmp)){
			$first_date = 0;
		}else{
			$first_date = end($tmp);
		}
		//$carbon_date->addDay();
		//$last_date = $carbon_date->format('Y-m-d');
		#Functions::_debug($tmp);

		$months = [];
		if($include_months){
			$months = $this->createMonthsCalendar($limit);
		}

		$ret = [
			'first_date' => $first_date,
			'last_date' => $last_date,
			'months' => $months,
			'dates' => $dates,
		];
		#Functions::_debug($ret);

		return $ret;

	}

	public function createMonthsCalendar($limit = 6){
		$months = [];

		$current = Carbon::now('Europe/Berlin');
		$y = $current->year;
		$m = $current->month;
		$d = 1;
		$begin = Carbon::create($y, $m, $d)->startOfDay();
		//Functions::_debug($begin_date);
		//$begin = Carbon::now('Europe/Berlin')->firstOfMonth(2);
		//$end->modify('+20 month');
		//$period = CarbonPeriod::create($begin, $end);

		$first_date = $current->toDateString();
		$labels['month'] = $current->locale('de')->format('M');
		$labels['year'] = $current->locale('de')->format('Y');
		while(true){
			if($current->weekday() > 1){
				$start_date = $current->toDateString();
				break;
			}else{
				$current->addDay();
			}
		}

		$i = 0;
		while(true){
			if(!$begin->isCurrentMonth()){
				$labels['month'] = $begin->locale('de')->format('M');
				$labels['year'] = $begin->locale('de')->format('Y');
				$first_date = $begin->toDateString();
				$start_date = $this->getStartDate($begin, $limit);
			}

			$months[] = [
				'first_date' => $first_date,
				'start_date' => $start_date,
				'labels' => $labels
			];

			$i++;
			$begin->addMonth();
			if($i == 20){
				break;
			}
		}

		/*foreach($period as $key => $date){
			$months[] = $date->toDateString();
		}*/

		return $months;
	}

	public function getStartDate($begin, $limit = 6){
		$first_date = Carbon::create($begin->toDateString(), 'Europe/Berlin');
		$tmp = [];
		while(true){
			$first_date->subDay();
			if($first_date->endOfDay()->timestamp < Carbon::now()->endOfDay()->timestamp){
				break;
			}
			if($first_date->weekday() > 1){
				$tmp[] = $first_date->toDateString();
			}
			if(count($tmp) >= $limit){
				break;
			}
		}
		unset($first_date);

		return end($tmp);
	}

	public function getWorkCalendar_old($suiten_name = 'all', $offset = 0, $limit = 6){
		$dates = [];
		$available_times = [];

		$start_time = 11;
		$end_time = 19;
		$interval = 2;

		for($i = $start_time; $i < $end_time; $i += $interval){
			$j = $i.':00-'.($i+$interval).':00';
			$available_times[$j] = 'available';
		}

		$carbon = Carbon::now();
		$period = CarbonPeriod::create($carbon->locale('de_GR'), '+1 MONTH');
		foreach($period as $key => $date){
			$d = $date->format('d.m.Y');
			$wd = $date->weekDay();
			if($wd > 1){
				$dates[$d] = [
					'id' => 'id'.str_replace('.', 'w', $d),
					'weekday' => $wd,
					'weekname' => $date->locale('de')->dayName,
					'times' => $available_times,
					'available_times' => count($available_times)
				];
			}
		}

		$events = $this->getEvents($suiten_name);
		#Functions::_debug($dates);
		#Functions::_debug($events);

		if($events['status'] == 200){
			if(!empty($events['results']) && is_array($events['results'])){
				foreach($events['results'] as $k => $result){
					$event_start_time = intval(str_replace(':', '', $result['start_time']));
					$event_end_time = intval(str_replace(':', '', $result['end_time']));
					#Functions::_debug([$result['start_date'], $event_start_time, $event_end_time]);
					if(isset($dates[$result['start_date']])){
						foreach($dates[$result['start_date']]['times'] as $times_interval => $status){
							$a = explode('-', $times_interval);
							$start_time = intval(str_replace(':', '', $a[0]));
							$end_time = intval(str_replace(':', '', $a[1]));
							#Functions::_debug([$start_time, $end_time]);
							if(
								($start_time > $event_start_time && $end_time < $event_end_time)
								||
								(($start_time > $event_start_time && $start_time < $event_end_time) || ($end_time > $event_start_time && $end_time < $event_end_time))
								||
								($start_time == $event_start_time || $end_time == $event_end_time)
								||
								($event_start_time > $start_time && $event_end_time < $end_time)
							){
								$dates[$result['start_date']]['times'][$times_interval] = 'reserved';
							}
							if(
								($event_start_time >= $start_time && $event_start_time < $end_time)
								||
								($event_end_time > $start_time && $event_end_time <= $end_time)
							){
								//$dates[$result['start_date']]['times'][$times_interval] = 'reserved';
							}
						}
						$c = array_count_values($dates[$result['start_date']]['times']);
						$dates[$result['start_date']]['available_times'] = isset($c['available']) ? $c['available'] : 0;

						/*$j = $result['start_time'].'-'.$result['end_time'];
						$dates[$result['start_date']]['times'][$j] = 'reserved';
						$dates[$result['start_date']]['available_times'] -= 1;*/
					}
				}
			}
		}

		#Functions::_debug($limit);
		if(!empty($dates)){
			$_dates = $dates;
			$dates = [];
			$i = 0;
			foreach($_dates as $k => $date){
				if($i >= $offset && $i < $offset+$limit){
					$dates[$k] = $date;
				}
				$i++;
			}
		}
		#Functions::_debug($dates);

		return $dates;

	}

}
