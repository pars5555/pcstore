<?php
	require_once("class.JavaScriptPacker.php");
	
	$conf = simplexml_load_file("builder.cfg.xml");
	$outBuf="";
	$command = "perl JSDoc/jsdoc.pl --no-sources -d docs/";
	$generateDoc = false;
	foreach($conf->builder as $builder){
		if($_REQUEST["filename"] != trim($builder->output->file)){
			continue;
		}
		
		

		if(trim((string)$builder->generate_doc)=="true"){
			$command .= (string)$builder->generate_doc["package"];
			foreach($builder->input->files->file as $inputFile){
				$isDoc = (string)$inputFile["doc"];
				if($isDoc === "true"){
					$inputFile=trim($inputFile);
					$command.= " $inputFile";
				}
			}
			$generateDoc = true;
		}
		
		if(trim($builder->debug)=="false"){
			$outBuf="";
			foreach($builder->input->files->file as $inputFile){
				$inputFile=trim($inputFile);
				$inF = fopen($inputFile, "r");
				$outBuf .= fread($inF, filesize($inputFile));
				$outBuf.="\n\r";
				fclose($inF);
	
			}
			if(trim($builder->output->compress)=="true"){
				$packer = new JavaScriptPacker($outBuf);
				$outBuf = $packer->pack();
			}
			$outF = fopen(trim($builder->output->file), "w");
			fwrite ($outF, $outBuf);
			fclose($outF);
//----redirecting to the new created file
			header("Location: ".$_REQUEST["filename"]);
			break;
		}
		else{
			foreach($builder->input->files->file as $inputFile){
				$inputFile=trim($inputFile);
				echo("document.write('<script type=\"text/javascript\" src=\"/js/$inputFile\"></script>');\n\r");
			}
			
		}
		if($generateDoc){
			exec($command);
		}

	}
?>