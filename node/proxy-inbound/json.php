<?php

$streams[0]['channel_name'] = 'FrankSpeech TV1';
$streams[0]['rtmp_guid'] = '4e9d57f7-448a-461d-a999-090ad4127c02';
$streams[0]['hls_guid'] = 'f203843d-3a61-4e6f-abea-7f8e522ac969';

$streams[1]['channel_name'] = 'FrankSpeech TV1';
$streams[1]['rtmp_guid'] = 'c2ce0861-2828-45b1-8e18-7198d05033db';
$streams[1]['hls_guid'] = '4b5b4705-8fb4-4f71-983b-6e481d1ea089';

$streams[3]['channel_name'] = 'FrankSpeech TV3';
$streams[3]['rtmp_guid'] = 'b9e51cf9-a9f5-4c6f-9559-0449d729867a';
$streams[3]['hls_guid'] = '99cb3160-0e38-43b2-a716-f1b97ccae412';

$streams[4]['channel_name'] = 'Zeus Demo Channel';
$streams[4]['rtmp_guid'] = 'f3fbca92-e507-4cb0-848d-cf93e3e7a3de';
$streams[4]['hls_guid'] = '387dddd4-b05d-433f-8b58-3b7ec02f0244';

$json = json_encode( $streams , true );

echo $json;