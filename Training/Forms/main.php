<?php

	$classification_arr = ["","Field Note","Survey Calculation","Legal Document","Map/Blueprint","Folder Cover","Separation Sheet","Envelope/Binding","Note","Stencil","Legal Document Draft","Correspondence","Legal Description"];
	$classification_desc = ["",
							"An actual page from a field book or a typed report of field book notes. They are often titled 'Field Notes' or is a list of survey point information.",
							"Recorded arithmetic pertaining to a survey. Often contains sketches and on yellow legal paper.",
							"Typed and signed documents pertaining to a survey, land tenure or sale, or work contract. Often contains an official stamp or notary.",
							"This classification only pertains to large sized maps, smaller map draft will be classified as 'Survey Calculation' because they are considered a sketch.",
							"A scanned copy of the original Job Folder.",
							"An index sheet provided by the Mary & Jeff Bell Library at Texas A&M University - Corpus Christi denoting a document who's physical condition is too poor to be scanned. The original map or document can only be accessed on-site in person.",
							"Can classify anything from an envelope to a taped piece of paper used to bind documents. Envelopes and bindings that are blank and contain no information are not scanned.",
							"A document that contains minimal information and cannot be otherwise classified.",
							"A document that is used to replicate specific fonts, symbols, or texts.",
							"A legal document that has not be officiated, or contains review marks.",
							"A document that appears to be conversation. Often an official telegram, but can still be messages left at hotels or offices for the surveyor or another contact.",
							"A document which is a written geographical description of a property for the purpose of identifying the property for legal transactions."	
							];

	//return XML directory, given username
	function XMLfilename($username)
	{
		return $username . ".xml";
	}



?>