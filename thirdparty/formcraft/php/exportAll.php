<?
chdir('../');
require_once('config.fc.php');


function outputCSV($data) {
	$outputBuffer = fopen("php://output", 'w');
	foreach($data as $val) 
	{
		fputcsv($outputBuffer, $val);
	}
	fclose($outputBuffer);
}

$mysub = ORM::for_table('submissions')->find_many();
$mysubr = ORM::for_table('submissions')->where('seen','1')->find_many();


$line[1][1] = 'Submission ID';
$line[1][2] = 'Read';
$line[1][3] = 'Date';
$line[1][4] = 'Location';
$line[1][5] = 'Form Name';
$line[1][6] = 'Field';
$line[1][7] = 'User Data';

$skey = 1;

foreach ($mysub as $key=>$row)
{
	$key++;

	$row_id = $row['form_id'];


$form_name = ORM::for_table('builder')->where('id',$row_id)->find_one()->name;


	if ($row['seen']=='1')
		{  $seen = 'Read';	}
	else { $seen = 'Unread'; }

	$new = json_decode($row['content'], 1);

	foreach ($new as $value)
	{
		$skey++;

		if ( !(empty($value['type'])) && !($value['type']=='captcha') )
		{


			if ($value['label']=='location' && $value['type']=='hidden' && isset($temp_key))
			{
				$line[$temp_key][4] = $value['value'];
			}
			if (!($set_it[$key]) && $value['label']!='location')
			{
				$set_it[$key]=1;
				$line[$key*$skey][1] = $row['id'];
				$line[$key*$skey][2] = $seen;
				$line[$key*$skey][3] = $row['added'];
				$line[$key*$skey][4] = '';
				$line[$key*$skey][5] = $form_name;
				$line[$key*$skey][6] = urldecode($value['label']);
				$line[$key*$skey][7] = urldecode($value['value']);
				$temp_key = $key*$skey;
			}
			else
			{
				if ($value['label']!='location' && $value['type']!='hidden')
				{
					$line[$key*$skey][1] = '';
					$line[$key*$skey][2] = '';
					$line[$key*$skey][3] = '';
					$line[$key*$skey][4] = '';
					$line[$key*$skey][5] = '';
					$line[$key*$skey][6] = urldecode($value['label']);
					$line[$key*$skey][7] = urldecode($value['value']);					
				}

			}

		}

	}

}


$header = 'Form Submission Data';
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=Submissions.csv");
header("Pragma: no-cache");
header("Expires: 0");
$line = outputCSV($line);
?>