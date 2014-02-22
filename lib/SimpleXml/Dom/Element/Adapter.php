<?php
/**
 * TODO: Namespaces support, proble with "if($simpleXmlElement)" (check tests), problem with typecast to Array (check tests), json_encode and decode
 * cast to bool, cast to int (not possible without a custom extension I guess)
 * @author Enrique Piatti
 */
class SimpleXml_Dom_Element_Adapter
	implements Iterator, ArrayAccess, Countable
	// ,JsonSerializable		// we need PHP >= 5.4 for this
{
	/** @var array of DOMElement */
	protected $_domElements = array();
	protected $_allowArrayAccessGreaterThanZero = true;		// set to false to get a small optimization if not using $xml->node[ > 0]
	/** @var  array of DOMAttr */
	protected $_domAttributes = array();
	/** @var  bool */
	protected $_singleNode = false;
	protected $_position = 0;

	/** @var  string
	 * this is a hack for creating an element while getting it doesn't exist
	 * example: $xml->notexistentelement['attr'] = 'value';		will end with: <notexistentelement attr="value"/>
	 */
	protected $_pendingElement;
	/** @var  DOMElement */
	protected $_pendingParent;

	/**
	 * @param $domNode DOMDocument | DOMElement | DOMAttr | string | array of DOMElement | array of DOMAttr
	 */
	public function __construct($domNode)
	{
		if($domNode instanceof DOMDocument){
			$domNode = $domNode->documentElement;
		}
		elseif(is_string($domNode)){
			$document = new DOMDocument();
			$document->loadXML($domNode);
			$domNode = $document->documentElement;
		}
		$this->_init($domNode);
	}

	/**
	 * @param $pendingElementName string
	 * @return $this
	 */
	protected function _createNewPendingElement($pendingElementName)
	{
		//$element = new SimpleXml_Dom_Element_Adapter(null);
		$element = self::_createObject(null, get_class($this));
		$element->_pendingElement = $pendingElementName;
		$element->_pendingParent = $this->_getDomElement();
		return $element;
	}


	protected function _init($domNode)
	{
		if(!$domNode){
			$domNode = null;
		}
		$isArray = is_array($domNode);
		$isAttr = $isArray ? ($domNode[0] instanceof DOMAttr) : ($domNode instanceof DOMAttr);

		if( ! $isArray){
			$domNode = array($domNode);
			$this->_singleNode = true;
		}

		if($isAttr){
			$this->_domAttributes = $domNode;
			$this->_domElements = array($this->_domAttributes[0]->ownerElement);
		}
		else {
			$this->_domElements = $domNode;
		}
	}


	/**
	 * @return DOMElement
	 */
	protected function _getDomElement()
	{
		return $this->_domElements[0];
	}

	/**
	 * @return DOMAttr
	 */
	protected function _getDomAttr()
	{
		return $this->_domAttributes ? $this->_domAttributes[0] : null;
	}


	/**
	 * @param $document DOMDocument
	 * @return SimpleXml_Dom_Element_Adapter
	 */
	public static function createFromDocument($document)
	{
		$className = get_called_class();
		return self::_createObject($document, $className);
		//return new SimpleXml_Dom_Element_Adapter($document->documentElement);
	}


	protected static function _createObject($element, $className = null)
	{
		if(!$className){
			$className = get_called_class();	// get_class($this);
		}
		return new $className($element);
	}

	public static function loadFromFile($filePath)
	{
		$document = new DOMDocument();
		$document->load(realpath($filePath));
		return self::createFromDocument($document);
	}

	/**
	 * @param $xml
	 * @param string|null $elementClass
	 * @return SimpleXml_Dom_Element_Adapter
	 */
	public static function loadFromString($xml, $elementClass = null)
	{
		$document = new DOMDocument();
		$document->loadXML($xml);
		if( ! $elementClass){
			$elementClass = "SimpleXml_Dom_Element_Adapter";
		}
		// PHP 5.3+
		static $isPhp53 = null;
		if($isPhp53 === null){
			$isPhp53 = version_compare(phpversion(), '5.3.0') >= 0;
		}

		if($isPhp53){
			return $elementClass::createFromDocument($document);
		}
		else {
			throw new Exception('You need at least PHP 5.3 to use '.get_class());
			// I think is is faster than Reflection:
//			return call_user_func(array($elementClass, 'createFromDocument'), $document);
//			$reflectionMethod = new ReflectionMethod($elementClass, 'createFromDocument');
//			//$reflectionMethod->setAccessible(true);
//			return $reflectionMethod->invoke(null, $document);
		}

	}

	/**
	 * @return DOMDocument
	 */
	protected function _getDocument()
	{
		return $this->_getDomElement() ? $this->_getDomElement()->ownerDocument : null;
	}
	
	
	/**
	 * @param $name
	 * @return SimpleXml_Dom_Element_Adapter
	 */
	public function __get($name)
	{

		if($this->_getDomAttr()){
			$attribute = null;
			foreach($this->_domAttributes as $attr){
				/** @var $attr DOMAttr */
				if($attr->name == $name){
					$attribute = $attr;
					break;
				}
			}
			//return self::_createObject($attribute);
			return self::_createObject($attribute, get_class($this));
			//return new SimpleXml_Dom_Element_Adapter($attribute);
		}

		elseif($this->_getDomElement())
		{
			$nodes = array();
			$childNodes = $this->_getDomElement()->childNodes;
			foreach($childNodes as $childNode){
				/** @var $childNode DOMNode */
				if($childNode instanceof DOMElement){
					if($childNode->nodeName == $name){
						$nodes[] = $childNode;
						if( ! $this->_allowArrayAccessGreaterThanZero){		// small optimization?
							break;
						}
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
			if( ! $nodes){

				// returning null so we can use if($element)
				// return null;
				// returning array() so we can use it inside an if and also inside a foreach
				return array();

				//return $this->_createNewPendingElement($name); // new SimpleXml_Dom_Element_Adapter($name, $this->_getDomElement());
			}
			//return new SimpleXml_Dom_Element_Adapter($nodes);
			return self::_createObject($nodes, get_class($this));
		}
		// dummy Element
		return $this;
	}


	public function __set($name, $value)
	{
		if($this->_getDomAttr()){
			foreach($this->_domAttributes as $attr){
				/** @var $attr DOMAttr */
				if($attr->name == $name){
					$attr->value = $value;
				}
			}
		}
		elseif($this->_getDomElement()){
			$this->addChild($name, $value);
		}
	}

	public function __isset($name)
	{
		if($this->_getDomAttr()){
			$attribute = null;
			foreach($this->_domAttributes as $attr){
				/** @var $attr DOMAttr */
				if($attr->name == $name){
					return true;
				}
			}
		}

		elseif($this->_getDomElement())
		{
			$childNodes = $this->_getDomElement()->childNodes;
			foreach($childNodes as $childNode){
				/** @var $childNode DOMNode */
				if($childNode instanceof DOMElement){
					if($childNode->nodeName == $name){
						return true;
					}
				}
			}
		}
		return false;
	}

	public function __unset($name)
	{
		if($this->_getDomAttr()){
			$attribute = null;
			foreach($this->_domAttributes as $key => $attr){
				/** @var $attr DOMAttr */
				if($attr->name == $name){
					$this->_getDomElement()->removeAttribute($name);
					unset($this->_domAttributes[$key]);
					return;
				}
			}
		}

		elseif($domElement = $this->_getDomElement())
		{
			$nodes = $domElement->getElementsByTagName($name);
			$domElemsToRemove = array();
			foreach ( $nodes as $node ) {
				$domElemsToRemove[] = $node;
			}
			foreach( $domElemsToRemove as $child ){
				$child->parentNode->removeChild($child);
			}
		}
	}


	public function __clone()
	{
		$dummy = '';
	}

	/**
	 * returns a SimpleXml_Dom_Element_Adapter object representing the child added to the XML node
	 * @param $name
	 * @param null $value
	 * @return $this|SimpleXml_Dom_Element_Adapter
	 */
	public function addChild($name, $value = null)
	{
		if( ! $this->_getDomElement()){
			return $this;
		}

		$node = $this->_getDocument()->createElement($name);
		$newNode = $this->_getDomElement()->appendChild($node);
		if($value){
			$newNode->nodeValue = $value;
		}
//		$newText = $this->getDocument()->createTextNode($value);
//		$newNode->appendChild($newText);
		//return new SimpleXml_Dom_Element_Adapter($newNode);
		return self::_createObject($newNode, get_class($this));
	}

	/**
	 * If the attribute already exists, addAttribute does nothing.
	 * @param $name
	 * @param null $value
	 */
	public function addAttribute($name, $value = null)
	{
		if( ! $this->_getDomElement()){
			return;
		}
		if( ! $this->_getDomElement()->getAttribute($name)){
			$newAttr = $this->_getDomElement()->setAttribute($name, $value);
		}else {
			// mimic warning from SimpleXMLElement::addAttribute() ?
			trigger_error(get_class($this)."::addAttribute(): Attribute already exists", E_USER_WARNING);
		}
	}


	public function asXML($filename = null)
	{
		if($filename){
			throw new Exception(get_class($this)."::asXML() with filename is not implemented");
		}

		if( ! $this->_getDomElement()){
			return '';
		}

		if($this->_getDomElement() === $this->getRootElement()){
			return $this->_getDocument()->saveXML();
		}
		return $this->_getDocument()->saveXML($this->_getDomElement());
//		else {
//			$newdoc = new DOMDocument;
//			$node = $newdoc->importNode($this->_domElement, true);
//			$newdoc->appendChild($node);
//			return $newdoc->saveXML();
//		}

	}


	public function getRootElement()
	{
		$doc = $this->_getDocument();
		return $doc ? $doc->documentElement : null;
	}

	public function getName()
	{
		return $this->_getDomAttr() !== null ? $this->_getDomAttr()->name : ($this->_getDomElement() ? $this->_getDomElement()->tagName : '');
	}

	/**
	 * @return $this|null|SimpleXml_Dom_Element_Adapter
	 */
	public function attributes()
	{
		if($this->_getDomAttr()){
			return array();
		}

		if( ! $this->_getDomElement()){
			return $this;
		}

		if ($this->_getDomElement()->hasAttributes()) {
			$attributes = array();
			foreach ($this->_getDomElement()->attributes as $attr) {
				$attributes[] = $attr;
				//$name = $attr->nodeName;
				//$value = $attr->nodeValue;

			}
			//return new SimpleXml_Dom_Element_Adapter($attributes);
			return self::_createObject($attributes, get_class($this));
		}

		//return new SimpleXml_Dom_Element_Adapter(null);
		//return self::_createObject(null, get_class($this));
		return array();
	}

	public function children()
	{
		//return new SimpleXml_Dom_Element_Adapter($this->_getChildElements() ? : null);
		$children = $this->_getChildElements();
		if( ! $children){
			return array();		// this will simulate the if($obj->children()) and will work also with foreach($obj->children() as $child)
		}
		return self::_createObject($children, get_class($this));
	}


	protected function _getChildElements()
	{
		$children = array();
		if($this->_getDomElement()){
			$childNodes = $this->_getDomElement()->childNodes;
			foreach($childNodes as $childNode){
				/** @var $childNode DOMNode */
				if($childNode instanceof DOMElement){
					$children[] = $childNode;
				}
			}
		}
		return $children;
	}

	public function current()
	{
		if($this->_getDomAttr()){
			$node = $this->_domAttributes[$this->_position];
		}
		elseif($this->_singleNode){
			// iterate children
			$children = $this->_getChildElements();
			$node = $children[$this->_position];
		}
		else {
			// iterate current array
			$node = $this->_domElements[$this->_position];
		}
		//return new SimpleXml_Dom_Element_Adapter($node);
		return self::_createObject($node, get_class($this));

	}


	public function next()
	{
		++$this->_position;
	}


	public function key()
	{
		if($this->_getDomAttr()){
			$key = $this->_domAttributes[$this->_position]->name;
		}
		elseif($this->_singleNode){
			$children = $this->_getChildElements();
			$key = $children[$this->_position]->tagName;
		}
		else {
			$key = $this->_domElements[$this->_position]->tagName;
		}
		return $key;
	}


	public function valid()
	{
		if($this->_getDomAttr()){
			return isset($this->_domAttributes[$this->_position]);
		}
		elseif($this->_singleNode){
			$children = $this->_getChildElements();
			return $children && isset($children[$this->_position]);
		}
		else {
			return isset($this->_domElements[$this->_position]);
		}
	}


	public function rewind()
	{
		$this->_position = 0;
	}

	/**
	 * Returns the number of elements of an element.
	 */
	public function count()
	{
		$count = 0;
		if($this->_getDomAttr()){
			$count = count($this->_domAttributes);
		}
		elseif( ! $this->_getDomElement()){
			$count = 0;
		}
		else {
			if($this->_singleNode){
				foreach($this->_getDomElement()->childNodes as $node){
					if($node instanceof DOMElement){
						$count++;
					}
				}
			}
			else {
				$count = count($this->_domElements);
			}
		}

		return $count;
	}


	/**
	 * Returns text content that is directly in this element. Does not return text content that is inside this element's children.
	 * @return string
	 */
	public function __toString()
	{
		if($this->_getDomAttr()){
			return $this->_getDomAttr()->value;
		}
		elseif($this->_getDomElement()) {
			// $this->_getDomElement()->textContent;
			// $this->_getDomElement()->nodeValue;
			$text = '';
			foreach($this->_getDomElement()->childNodes as $node) {
				/** @var $node DOMNode */
				if ($node->nodeType != XML_TEXT_NODE && $node->nodeType != XML_CDATA_SECTION_NODE) {
					continue;
				}
				//$text .= $node->nodeValue;
				$text .= $node->textContent;
			}
			return $text;
		}
		return '';
	}


	public function offsetExists($offset)
	{
		if( ! $this->_getDomElement()){
			return false;
		}

		if(is_integer($offset)){
			return isset($this->_domElements[$offset]);
		}
		else {
			// attributes
			return (bool) $this->_getDomElement()->getAttributeNode($offset);
		}
	}

	public function offsetGet($offset)
	{
		if( ! $this->_getDomElement()){
			$node = null;
		}

		if(is_integer($offset)){
			$node = isset($this->_domElements[$offset]) ? $this->_domElements[$offset] : null;
		}
		else {
			// attributes
			$attr = $this->_getDomElement()->getAttributeNode($offset);
			$node = $attr ? : null;
		}
		
		//return new SimpleXml_Dom_Element_Adapter($node);
		if( ! $node){
			// return null so we can use if($element) in a similar way to SimpleXMLElement
			return null;
		}
		return self::_createObject($node, get_class($this));
	}

	public function offsetSet($offset, $value)
	{
		if(is_integer($offset)){
			/** @var DOMElement $node */
			$node = isset($this->_domElements[$offset]) ? $this->_domElements[$offset] : null;
			$node->nodeValue = (string)$value;
		}
		elseif($offset === null){	// []
			if($this->_getDomElement()){
				if($this->_getDomElement() === $this->getRootElement()){
					throw new Exception('Cannot create unnamed attribute');		// this is similar to the original Fatal error thrown by SimpleXmlElement
				}
				$node = $this->_getDocument()->createElement($this->getName());
				$node->nodeValue = (string)$value;
				$this->_getDomElement()->parentNode->appendChild($node);
			}
		}
		else {
			// if parent element doesn't exist yet, create it
			if($this->_pendingElement){
				$node = $this->_pendingParent->ownerDocument->createElement($this->_pendingElement);
				$newNode = $this->_pendingParent->appendChild($node);
				$this->_init($newNode);
			}
			// If the attribute does not exist, it will be created
			$attr = $this->_getDomElement()->setAttribute($offset, (string)$value);
		}
	}

	public function offsetUnset($offset)
	{

		if( ! $this->_getDomElement()){
			return;
		}

		if(is_integer($offset)){
			if( isset($this->_domElements[$offset])){
				unset($this->_domElements[$offset]);
			}
		}
		else {
			// attributes
			$this->_getDomElement()->removeAttribute($offset);
		}

	}


	/**
	 * @param $path string
	 * @return array of SimpleXml_Dom_Element_Adapter
	 */
	public function xpath($path)
	{
		$elements = array();
		if( $this->_getDomElement())
		{
			$xpath = new DOMXPath($this->_getDocument());
			/** @var DOMNodeList $result */
			$result = $xpath->query($path, $this->_getDomElement());
			// $result->item(0);
			foreach($result as $node){
				/** @var $node DomElement */
				//$elements[] = new SimpleXml_Dom_Element_Adapter($node);
				$elements[] = self::_createObject($node, get_class($this));
			}
		}
		return $elements;

	}


	/**
	 * This method is an alias of: self::asXML()
	 * @param null $filename
	 * @return string
	 */
	public function saveXML($filename = null)
	{
		return $this->asXML($filename);
	}

	/**
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 */
	public function jsonSerialize()
	{
		// TODO: Implement jsonSerialize() method.
	}


}

