<?php

class CartTest extends PHPUnit_Framework_TestCase {
	
	protected $SESSION;
	
	protected function setSessionName($name) { if (trim($name) != "") $this->SESSION['name'] = $name; }

	protected function setSessionAddress($address) { if (trim($address) != "") $this->SESSION['address'] = $address; }

	protected function setSessionMail($mail) { if ($this->isMail($mail)) $this->SESSION['mail'] = $mail; }

	protected function setSessionPhone($phone) { if ($this->isPhone($phone)) $this->SESSION['phone'] = $phone; }

	protected function getClientName() { return (isset($this->SESSION['name']) ? $this->SESSION['name'] : NULL); }

	protected function getClientAddress() { return (isset($this->SESSION['address']) ? $this->SESSION['address'] : NULL); }

	protected function getClientMail() { return (isset($this->SESSION['mail']) ? $this->SESSION['mail'] : NULL); }

	protected function getClientPhone() { return (isset($this->SESSION['phone']) ? $this->SESSION['phone'] : NULL); }
	
	protected function isMail($mail) {
		if (strlen($mail) < 5)
			return FALSE;
		if (substr_count($mail, '@') != 1 || substr_count($mail, '.') == 0)
			return FALSE;
		$strudel = strpos($mail, '@');
		if ($strudel == 0 || $strudel > strlen($mail) - 4)
			return FALSE;
		$dot = strpos($mail, '@', $strudel);
		if ($dot === FALSE || $dot == $strudel + 1 || $dot == strlen($mail) - 1)
			return FALSE;
		if ($mail[strlen($mail)-1] == '.')
			return FALSE;
		return TRUE;
	}
	
	protected function isPhone($phone) {
		$prefix = strpos($phone, '054');
		if ($prefix === FALSE || $prefix !== 0)
			return FALSE;
		if (strlen($phone) != 10)
			return FALSE;
		return (ctype_digit($phone));
	}

	/*****     $this->SESSION['orders']     *****/
	// holds the ids of the already sent orders

	protected function checkSentOrders() {
		if(!array_key_exists('orders', $this->SESSION))                        
			$this->SESSION['orders'] = array();  
	}

	protected function addSentOrder($id) {
		$this->checkSentOrders();
		if (!in_array($id, $this->SESSION['orders']))
			array_push($this->SESSION['orders'], $id);
	}

	protected function getSentOrders() {
		if(!array_key_exists('orders', $this->SESSION) || empty($this->SESSION['orders']))
			return NULL;
		return $this->SESSION['orders'];
	}

	/*****     $this->SESSION['cart']     *****/

	protected function cartIsEmpty() {
		$sub_cart = $this->getSubCart();
		$print_cart = $this->getPrintCart();
		if (!is_null($sub_cart) || !is_null($print_cart)) {
			if ($sub_cart != NULL) {
				foreach ($sub_cart as $pid => $sub_cart_product) {
					foreach ($this->SESSION['cart']['sublimation'][$pid] as $prod) {
						if (array_key_exists('quantity', $prod) && $prod['quantity'] > 0)
							return FALSE;
					}
				}
			}
		
			if ($print_cart != NULL) {
				foreach ($print_cart as $cid => $print_cart_cat) {
					foreach ($this->SESSION['cart']['print'][$cid] as $prod) {
						if (array_key_exists('quantity', $prod) && $prod['quantity'] > 0)
							return FALSE;
					}
				}
			}
		}
		return TRUE;        
	}

	protected function emptyCart() {
		$sub_cart = $this->getSubCart();
		$print_cart = $this->getPrintCart();
		if (!is_null($sub_cart) || !is_null($print_cart)) {
			if ($sub_cart != NULL) {
				foreach ($sub_cart as $pid => $sub_cart_product) {
					$dlt_count = 0;
					foreach ($this->SESSION['cart']['sublimation'][$pid] as $key=>$prod) {
						array_splice($this->SESSION['cart']['sublimation'][$pid], $key- $dlt_count, 1);
						$dlt_count++;
					}
				}
				unset($this->SESSION['cart']['sublimation']);
			}
		
			if ($print_cart != NULL) {
				foreach ($print_cart as $cid => $print_cart_cat) {
					$dlt_count = 0;
					foreach ($this->SESSION['cart']['print'][$cid] as $key=>$prod) {
						array_splice($this->SESSION['cart']['print'][$cid], $key - $dlt_count, 1);
						$dlt_count++;
					}
				}
				unset($this->SESSION['cart']['print']);
			}
		}                    
		unset($this->SESSION['cart']);
	}

	protected function checkService($service, $id) {
		if(!array_key_exists('cart', $this->SESSION))                        //if the cart doesn't exist yet
			$this->SESSION['cart'] = array();                   
		if (!array_key_exists($service, $this->SESSION['cart']))             //if the user didn't enter any service product in the cart yet
			$this->SESSION['cart'][$service] = array();
		if (!array_key_exists($id, $this->SESSION['cart'][$service]))        //if the user didn't enter any service product of this kind yet 
			$this->SESSION['cart'][$service][$id] = array();
	}

	/*****     $this->SESSION['cart']['sublimation']     *****/
	/*
	 * $this->SESSION['cart']['sublimation'][product-id][index] = (design-url, quantity)
	 */

	protected function initializeSubProduct($pid) {
		$this->checkService('sublimation', $pid);
		if (/*is_null(get_sub_product($pid)) || count(get_sub_product($pid)) == 0 ||*/ empty($this->SESSION['cart']['sublimation'][$pid])) {
			unset($this->SESSION['cart']['sublimation'][$pid]);
			return;
		}
		$dlt_count = 0;
		foreach ($this->SESSION['cart']['sublimation'][$pid] as $key=>$prod) {
			if (!array_key_exists('quantity', $prod) || $prod['quantity'] == -1) {
				//if (array_key_exists('quantity', $prod))
				//    unlink(url_to_path($prod['url']));
				array_splice($this->SESSION['cart']['sublimation'][$pid], $key- $dlt_count, 1);
				$dlt_count++;
			}
		}
	}

	protected function checkSubProduct($pid) {
		$this->checkService('sublimation', $pid);
	}

	protected function getSubCart() {
		if(!array_key_exists('cart', $this->SESSION) || !array_key_exists('sublimation', $this->SESSION['cart']))
			return NULL;
		if (empty($this->SESSION['cart']['sublimation']) || count($this->SESSION['cart']['sublimation']) == 0)
			return NULL;
		foreach ($this->SESSION['cart']['sublimation'] as $pid=>$sub_product)
			$this->initializeSubProduct($pid);
		return $this->SESSION['cart']['sublimation'];   
	}

	protected function addDesign($pid, $url) {
		if (!is_int($pid) || $pid < 1)
			return;
		$this->checkSubProduct($pid);
		$new_design = array (
			'url' => $url,
			'quantity' => -1
		);
		array_push($this->SESSION['cart']['sublimation'][$pid], $new_design);
	}

	protected function setDesignQuantity($pid, $index, $quantity) {
		if (!is_int($pid) || !is_int($index))
			return;
		if ($pid < 1 || $index < 0)
			return;
		if (!is_int($quantity) || $quantity < 0)
			$quantity = 0;
		if ($index >= count($this->SESSION['cart']['sublimation'][$pid]))
				return;
		$this->SESSION['cart']['sublimation'][$pid][$index]['quantity'] = $quantity;
	}

	protected function getDesignNumber($pid) {
		$this->checkSubProduct($pid);
		return count($this->SESSION['cart']['sublimation'][$pid]);
	}

	protected function getSublimationField($pid, $index, $field) {
		if (!is_int($pid) || !is_int($index))
			return NULL;
		if ($pid < 1 || $index < 0)
			return NULL;
		$this->checkSubProduct($pid);
		if ($index >= count($this->SESSION['cart']['sublimation'][$pid]))
			return NULL;
		return $this->SESSION['cart']['sublimation'][$pid][$index][$field];
	}

	protected function getSublimationDesign($pid, $index) { return $this->getSublimationField($pid, $index, 'url'); }

	protected function getSublimationQuantity($pid, $index) { return $this->getSublimationField($pid, $index, 'quantity'); }

	/*****     $this->SESSION['cart']['print']     *****/
	/*
	 * $this->SESSION['cart']['print'][category-id][index] = (filename, file-url, product-id, quantity, pages)
	 */

	protected function initializePrintCategory($cid) {
		$this->checkService('print', $cid);
		if (/*is_null(get_print_category($cid)) || count(get_print_category($cid)) == 0 ||*/ empty($this->SESSION['cart']['print'][$cid])) {
			unset($this->SESSION['cart']['print'][$cid]);
			return;
		}
		$dlt_count = 0;
		foreach ($this->SESSION['cart']['print'][$cid] as $key=>$prod) {
			if (!array_key_exists('quantity', $prod) || $prod['quantity'] == -1) {
				//if (array_key_exists('filepath', $prod))
				//    unlink(url_to_path($prod['filepath']));
				array_splice($this->SESSION['cart']['print'][$cid], $key - $dlt_count, 1);
				$dlt_count++;
			}
		}
	}

	protected function checkPrintCategory($cid) {
		$this->checkService('print', $cid);
	}

	protected function getPrintCart() {
		if(!array_key_exists('cart', $this->SESSION) || !array_key_exists('print', $this->SESSION['cart']))
			return NULL;
		if (empty($this->SESSION['cart']['print']) || count($this->SESSION['cart']['print']) == 0)
			return NULL;
		foreach ($this->SESSION['cart']['print'] as $cid=>$print_product)
			$this->initializePrintCategory($cid);
		return count($this->SESSION['cart']['print']) == 0 ? NULL : $this->SESSION['cart']['print'];   
	}

	protected function addFile($cid, $filename, $filepath) {
		if (!is_int($cid) || $cid < 1)
			return;
		$this->checkPrintCategory($cid);
		$new_file = array (
			'filename' => $filename,
			'filepath' => $filepath,
			'quantity' => -1,
			'pages' => 1,
			'pid' => 0
		);
		array_push($this->SESSION['cart']['print'][$cid], $new_file);
	}

	protected function removeFile($cid, $index) {
		if (!is_int($cid) || !is_int($index))
			return;
		if ($cid < 1 || $index < 0)
			return;
		$this->checkPrintCategory($cid);
		if ($index >= count($this->SESSION['cart']['print'][$cid]))
				return;
		//unlink(url_to_path($this->SESSION['cart']['print'][$cid][$index]['filepath']));
		array_splice($this->SESSION['cart']['print'][$cid], $index, 1);
	}

	protected function editFile($cid, $index, $filename, $filepath) {
		if (!is_int($cid) || !is_int($index))
			return;
		if ($cid < 1 || $index < 0)
			return;
		$this->checkPrintCategory($cid);
		$new_file = array (
			'filename' => $filename,
			'filepath' => $filepath,
			'quantity' => -1,
			'pid' => 0,
			'pages' => 1
		);
		$this->SESSION['cart']['print'][$cid][$index] = $new_file;
	}

	protected function getUploadNumber($cid) {
		if (!is_int($cid) || $cid < 1)
			return 0;
		$this->checkPrintCategory($cid);
		return count($this->SESSION['cart']['print'][$cid]);
	}

	protected function getPrintField($cid, $index, $field) {
		if (!is_int($cid) || !is_int($index))
			return NULL;
		if ($cid < 1 || $index < 0)
			return NULL;
		$this->checkSubProduct($cid);
		if ($index >= count($this->SESSION['cart']['print'][$cid]))
			return NULL;
		return $this->SESSION['cart']['print'][$cid][$index][$field];
	}

	protected function getPid($cid, $index) { return $this->getPrintField($cid, $index, 'pid'); }

	protected function getFilename($cid, $index) { return $this->getPrintField($cid, $index, 'filename'); }

	protected function getFilepath($cid, $index) { return $this->getPrintField($cid, $index, 'filepath'); }

	protected function getPrintQuantity($cid, $index) { return $this->getPrintField($cid, $index, 'quantity'); }

	protected function getNumPages($cid, $index) { return $this->getPrintField($cid, $index, 'pages'); }

	protected	function setPrintField($cid, $index, $field, $value) {
		if (!is_int($cid) || !is_int($index))
			return NULL;
		if ($cid < 1 || $index < 0)
			return NULL;
		$this->checkSubProduct($cid);
		if ($index >= count($this->SESSION['cart']['print'][$cid]))
			return;
		$this->SESSION['cart']['print'][$cid][$index][$field] = $value;
	}

	protected function setPrintPid($cid, $index, $pid) { $this->setPrintField($cid, $index, 'pid', $pid); }

	protected function setPrintQuantity($cid, $index, $q) {
		if (!is_int($q) || $q < 0)
			$q = 0;
		$this->setPrintField($cid, $index, 'quantity', $q);
	}

	protected function setPrintPages($cid, $index, $pagesno) {
		if (!is_int($pagesno) || $pagesno < 1)
			$pagesno = 1;
		$this->setPrintField($cid, $index, 'pages', $pagesno);
	}

    public function setUp(){ $this->SESSION = array(); }
    public function tearDown(){}
	
	public function testSetSessionName() {
		$this->assertTrue ($this->getClientName() == NULL);
		$this->setSessionName("Hannah Bellaiche");
		$this->assertEquals ($this->getClientName(), "Hannah Bellaiche");
		$this->setSessionName("Flora Bellaiche");
		$this->assertEquals($this->getClientName(), "Flora Bellaiche");
		$this->setSessionName("");
		$this->assertEquals($this->getClientName(), "Flora Bellaiche");
		$this->setSessionName("   ");
		$this->assertEquals($this->getClientName(), "Flora Bellaiche");
	}
	
	public function testGetSetSessionAddress() {
		$this->assertTrue ($this->getClientAddress() == NULL);
		$this->setSessionAddress("Jerusalem");
		$this->assertEquals ($this->getClientAddress(), "Jerusalem");
		$this->setSessionAddress("Paris");
		$this->assertEquals($this->getClientAddress(), "Paris");
		$this->setSessionAddress("");
		$this->assertEquals($this->getClientAddress(), "Paris");
		$this->setSessionAddress("  ");
		$this->assertEquals($this->getClientAddress(), "Paris");
    }
	
	public function testGetSetSessionMail() {
		$this->assertTrue ($this->getClientMail() == NULL);
		$this->setSessionMail("hannah.bellaiche@gmail.com");
		$this->assertEquals ($this->getClientMail(), "hannah.bellaiche@gmail.com");
		$this->setSessionMail("mezanfi@gmail.com");
		$this->assertEquals($this->getClientMail(), "mezanfi@gmail.com");
		$this->setSessionMail("mezanfigmail.com");
		$this->assertEquals($this->getClientMail(), "mezanfi@gmail.com");
		$this->setSessionMail("mezanfi@gmailcom");
		$this->assertEquals($this->getClientMail(), "mezanfi@gmail.com");
		$this->setSessionMail("mezanfigmailcom");
		$this->assertEquals($this->getClientMail(), "mezanfi@gmail.com");
		$this->setSessionMail("mezanfigmail.c@om");
		$this->assertEquals($this->getClientMail(), "mezanfi@gmail.com");
		$this->setSessionMail("mezanfigmail.com@");
		$this->assertEquals($this->getClientMail(), "mezanfi@gmail.com");
		$this->setSessionMail("mezanfi@gmail.com.");
		$this->assertEquals($this->getClientMail(), "mezanfi@gmail.com");
		$this->setSessionMail("@m.m");
		$this->assertEquals($this->getClientMail(), "mezanfi@gmail.com");
    }
	
	public function testGetSetSessionPhone() {
		$this->assertTrue ($this->getClientPhone() == NULL);
		$this->setSessionPhone("0547891227");
		$this->assertEquals ($this->getClientPhone(), "0547891227");
		$this->setSessionPhone("0549148293");
		$this->assertEquals($this->getClientPhone(), "0549148293");
		$this->setSessionPhone("054914829");
		$this->assertEquals($this->getClientPhone(), "0549148293");
		$this->setSessionPhone("");
		$this->assertEquals($this->getClientPhone(), "0549148293");
		$this->setSessionPhone("052914829");
		$this->assertEquals($this->getClientPhone(), "0549148293");
		$this->setSessionPhone("0549148a9");
		$this->assertEquals($this->getClientPhone(), "0549148293");
    }
	
	public function testCreateDesigns() {
		$pid = 1;		// the sublimation product id
		$this->initializeSubProduct($pid);
		$this->addDesign($pid, 'path/to/canvas#1.png');
		$this->addDesign($pid, 'path/to/canvas#2.png');
		$this->addDesign($pid, 'path/to/canvas#3.png');
		$this->assertTrue($this->SESSION['cart']['sublimation'][$pid][0]['quantity'] == -1);
		$this->assertTrue($this->SESSION['cart']['sublimation'][$pid][1]['quantity'] == -1);
		$this->assertTrue($this->SESSION['cart']['sublimation'][$pid][2]['quantity'] == -1);
		$this->addDesign(5.1, 'path/to/canvas#3.png');
		$this->assertFalse(isset($this->SESSION['cart']['sublimation'][5.1]));
		$this->addDesign(3.2, 'path/to/canvas#3.png');
		$this->assertFalse(isset($this->SESSION['cart']['sublimation'][3.2]));
		$this->addDesign('a5', 'path/to/canvas#3.png');
		$this->assertFalse(isset($this->SESSION['cart']['sublimation']['a5']));
		$quantities = array (2, 4, 1);
		for ($i = 0; $i < $this->getDesignNumber($pid); $i++)
			$this->setDesignQuantity($pid, $i, $quantities[$i]);
		$this->assertTrue($this->getSublimationDesign($pid, 0) == 'path/to/canvas#1.png' && $this->getSublimationQuantity($pid, 0) == 2);
		$this->assertTrue($this->getSublimationDesign($pid, 1) == 'path/to/canvas#2.png' && $this->getSublimationQuantity($pid, 1) == 4);
		$this->assertTrue($this->getSublimationDesign($pid, 2) == 'path/to/canvas#3.png' && $this->getSublimationQuantity($pid, 2) == 1);
		$this->assertFalse($this->cartIsEmpty());
		$this->setDesignQuantity(1.5, 0, 5);
		$this->assertTrue($this->getSublimationDesign($pid, 0) == 'path/to/canvas#1.png' && $this->getSublimationQuantity($pid, 0) == 2);
		$this->setDesignQuantity($pid, 0, -5);
		$this->assertTrue($this->getSublimationDesign($pid, 0) == 'path/to/canvas#1.png' && $this->getSublimationQuantity($pid, 0) == 0);
		$quantities = array (0, 0, 0);
		for ($i = 0; $i < $this->getDesignNumber($pid); $i++)
			$this->setDesignQuantity($pid, $i, $quantities[$i]);
		$this->assertTrue($this->getSublimationDesign($pid, 0) == 'path/to/canvas#1.png' && $this->getSublimationQuantity($pid, 0) == 0);
		$this->assertTrue($this->getSublimationDesign($pid, 1) == 'path/to/canvas#2.png' && $this->getSublimationQuantity($pid, 1) == 0);
		$this->assertTrue($this->getSublimationDesign($pid, 2) == 'path/to/canvas#3.png' && $this->getSublimationQuantity($pid, 2) == 0);
		$this->assertTrue($this->cartIsEmpty());
		$this->assertEquals($this->getSublimationDesign($pid, -1.5), NULL);
		$this->assertEquals($this->getSublimationDesign($pid, '-1a'), NULL);
	}
	
	public function testUploadFiles() {
		$cid = 1;
		$this->initializePrintCategory($cid);
		$this->assertEquals(1, 1);
		$this->assertEquals($this->getUploadNumber($cid), 0);
		$this->assertEquals($this->getUploadNumber(-1), 0);
		$this->assertEquals($this->getUploadNumber('a'), 0);
		$this->addFile($cid, 'filename#1', 'path/to/file#1.ext');
		$this->addFile($cid, 'filename#2', 'path/to/file#2.ext');
		$this->assertTrue($this->getPid($cid, 'a') == NULL);
		$this->assertEquals($this->getPid(-7, 0), NULL);
		$this->assertTrue($this->getPid($cid, 0) == 0 && $this->getPid($cid, 1) == 0);
		$this->assertTrue($this->getFilename($cid, 0) == 'filename#1' && $this->getFilename($cid, 1) == 'filename#2');
		$this->assertTrue($this->getFilepath($cid, 0) == 'path/to/file#1.ext' && $this->getFilepath($cid, 1) == 'path/to/file#2.ext');
		$this->assertTrue($this->getPrintQuantity($cid, 0) == -1 && $this->getPrintQuantity($cid, 1) == -1);
		$this->assertTrue($this->getNumPages($cid, 0) == 1 && $this->getNumPages($cid, 1) == 1);
		$this->editFile($cid, 0, 'newfilename', 'path/to/newfile.ext');
		$this->assertTrue($this->getFilename($cid, 0) == 'newfilename' && $this->getFilepath($cid, 0) == 'path/to/newfile.ext');
		$this->removeFile($cid, 0);
		$this->assertTrue($this->getFilename($cid, 0) == 'filename#2' && $this->getFilepath($cid, 0) == 'path/to/file#2.ext');
		$this->assertEquals($this->getUploadNumber($cid), 1);
		$this->addFile($cid, 'filename#3', 'path/to/file#3.ext');
		$this->addFile($cid, 'filename#4', 'path/to/file#4.ext');
		$pids = array(1, 2, 3);
		for ($i = 0; $i < 3; $i++)
			$this->setPrintPid($cid, $i, $pids[$i]);
		$this->assertTrue($this->getPid($cid, 0) == 1 && $this->getPid($cid, 1) == 2 && $this->getPid($cid, 2) == 3);
		$pages = array (-1, 0, 4);
		$quantities = array (1.5, 4, 7);
		for ($i = 0; $i < 3; $i++) {
			$this->setPrintQuantity($cid, $i, $quantities[$i]);
			$this->setPrintPages($cid, $i, $pages[$i]);
		}
		$this->assertTrue($this->getNumPages($cid, 0) == 1 && $this->getNumPages($cid, 1) == 1 && $this->getNumPages($cid, 2) == 4);
		$this->assertTrue($this->getPrintQuantity($cid, 0) == 0 && $this->getPrintQuantity($cid, 1) == 4 && $this->getPrintQuantity($cid, 2) == 7);
		$this->assertFalse($this->cartIsEmpty());
	}
}

?>
