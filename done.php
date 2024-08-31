<?php

$req = new rXMLRPCRequest(array(
	rTorrentSettings::get()->getOnInsertCommand(array('tline'.User::getUser(), getCmd('cat='))),
	rTorrentSettings::get()->getOnFinishedCommand(array('tline'.User::getUser(), getCmd('cat='))),
	rTorrentSettings::get()->getOnEraseCommand(array('tline'.User::getUser(), getCmd('cat=')))
	));
$req->run();
