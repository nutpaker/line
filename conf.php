<?php

$lineEndpoint = "https://notify-api.line.me/api/notify";

$lineNotifications = array
(
	"addition" => array
	(
		"message" => "Torrent was added\n".
					"Name: {name}\n".
					"Label: {label}\n".
					"Size: {size}\n".
					"Time: {added}\n".
					"Tracker: {tracker}",
	),
	"deletion" => array
	(
		"message" => "Torrent was deleted\n".
					"Name: {name}\n".
					"Label: {label}\n".
					"Size: {size}\n".
					"Downloaded: {downloaded}\n".
					"Uploaded: {uploaded}\n".
					"Ratio: {ratio}\n".
					"Creation: {creation}\n".
					"Added: {added}\n".
					"Finished: {finished}\n".
					"Tracker: {tracker}",
	),	
	"finish" => array
	(
		"message" => "Torrent was finished\n".
					"Name: {name}\n".
					"Label: {label}\n".
					"Size: {size}\n".
					"Downloaded: {downloaded}\n".
					"Uploaded: {uploaded}\n".
					"Ratio: {ratio}\n".
					"Creation: {creation}\n".
					"Added: {added}\n".
					"Finished: {finished}\n".
					"Tracker: {tracker}",
	),	
);