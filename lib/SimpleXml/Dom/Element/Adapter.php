<?php
/**
 *
 * @author Enrique Piatti
 */
class SimpleXml_Dom_Element_Adapter
	implements Iterator, Countable
{
	/**
	 * @var DOMElement
	 */
	protected $_domElement;


	public function __construct($domElement)
	{
		$this->_domElement = $domElement;
	}


	protected static function _createFromDocument($document)
	{
		return new SimpleXml_Dom_Element_Adapter($document->documentElement);
	}

	public static function loadFromFile($filePath)
	{
		$document = new DOMDocument();
		$document->load(realpath($filePath));
		return self::_createFromDocument($document);
	}

	public static function loadFromString($xml)
	{
		$document = new DOMDocument();
		$document->loadXML($xml);
		return self::_createFromDocument($document);
	}

	/**
	 * @return DOMDocument
	 */
	public function getDocument()
	{
		return $this->_domElement->ownerDocument;
	}
	
	
	/**
	 * @param $name
	 * @return SimpleXml_Dom_Element_Adapter
	 */
	public function __get($name)
	{
		$childNodes = $this->_domElement->childNodes;
		foreach($childNodes as $childNode){
			/** @var $childNode DOMNode */
			if($childNode instanceof DOMElement){
				if($childNode->nodeName == $name){
					return new SimpleXml_Dom_Element_Adapter($childNode);
				}
			}
		}
//		$query = "/{$this->_getRootElement()->tagName}/$name";
//		$xpath = new DOMXPath($this->_domDocument);
//		/** @var DOMNodeList $result */
//		$result = $xpath->query($query);
		// $result->item(0);
//		foreach($result as $node){
//			/** @var $node DomElement */
//			return new SimpleXml_Dom_Element_Adapter($node);
//		}
		return new SimpleXml_Dom_Element_Adapter(null);
	}


	public function __set($name, $value)
	{
		return $this->addChild($name, $value);
	}


	public function addChild($name, $value)
	{
		$node = $this->getDocument()->createElement($name);
		$newNode = $this->_domElement->appendChild($node);
		$newNode->nodeValue = $value;
//		$newText = $this->getDocument()->createTextNode($value);
//		$newNode->appendChild($newText);
		return $newNode;
	}

	public function addAttribute($name, $value)
	{
		$newAttr = $this->_domElement->setAttribute($name, $value);
		return $newAttr;
	}


	public function asXml()
	{
		return $this->getDocument()->saveXML();
	}

	public function getName()
	{
		return $this->_domElement->tagName;
	}

	public function attributes()
	{
		// TODO: return adapter for attributes
		if ($this->_domElement->hasAttributes()) {
			foreach ($this->_domElement->attributes as $attr) {
				$name = $attr->nodeName;
				$value = $attr->nodeValue;
			}
		}
	}

	public function children()
	{
		return $this->_domElement->childNodes;
	}


	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 *
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current()
	{
		// TODO: Implement current() method.
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 *
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next()
	{
		// TODO: Implement next() method.
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 *
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key()
	{
		// TODO: Implement key() method.
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 *
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid()
	{
		// TODO: Implement valid() method.
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 *
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind()
	{
		// TODO: Implement rewind() method.
	}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Count elements of an object
	 *
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * </p>
	 * <p>
	 * The return value is cast to an integer.
	 */
	public function count()
	{
		// TODO: Implement count() method.
	}


	public function __toString()
	{
		return $this->_domElement->nodeValue;
	}


}
