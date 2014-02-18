<?php
/**
 * @author Enrique Piatti
 */
// require_once 'PHPUnit/Autoload.php';
//require_once './app/Mage.php';
require_once dirname(__FILE__) . '/../app/Mage.php';	// this will run the autoload from Magento

class SimpleXml_Dom_Element_AdapterTest extends PHPUnit_Framework_TestCase
{

	protected $configXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<config>
    <global>
        <install>
            <date><![CDATA[Sat, 11 Jan 2014 15:40:18 +0000]]></date>
        </install>
        <resources>
            <default_setup>
                <connection>
                    <host><![CDATA[localhost]]></host>
                    <username><![CDATA[root]]></username>
                    <password/>
                    <dbname><![CDATA[magento_dom]]></dbname>
                    <initStatements><![CDATA[SET NAMES utf8]]></initStatements>
                    <model><![CDATA[mysql4]]></model>
                    <type><![CDATA[pdo_mysql]]></type>
                    <pdoType/>
                    <active>1</active>
                </connection>
            </default_setup>
        </resources>
        <resource>
            <connection>
                <types>
                    <pdo_mysql>
                        <adapter>Magento_Db_Adapter_Pdo_Mysql</adapter>
                        <class>Mage_Core_Model_Resource_Type_Db_Pdo_Mysql</class>
                        <compatibleMode>1</compatibleMode>
                    </pdo_mysql>
                </types>
            </connection>
        </resource>
        <models>
            <varien>
                <class>Varien</class>
            </varien>
            <core>
                <class>Mage_Core_Model</class>
                <resourceModel>core_resource</resourceModel>
            </core>
            <core_resource>
                <class>Mage_Core_Model_Resource</class>
                <deprecatedNode>core_mysql4</deprecatedNode>
                <entities>
                    <config_data>
                        <table>core_config_data</table>
                    </config_data>
                    <resource>
                        <table>core_resource</table>
                    </resource>
                </entities>
            </core_resource>
        </models>
        <blocks>
            <core>
                <class>Mage_Core_Block</class>
            </core>
            <catalog>
                <class>Mage_Catalog_Block</class>
            </catalog>
        </blocks>
        <helpers>
            <admin>
                <class>Mage_Admin_Helper</class>
            </admin>
        </helpers>
        <template>
            <email>
                <admin_emails_forgot_email_template translate="label" module="adminhtml">
                    <label>Forgot Admin Password</label>
                    <file>admin_password_reset_confirmation.html</file>
                    <type>html</type>
                </admin_emails_forgot_email_template>
                <currency_import_error_email_template translate="label" module="directory">
                    <label>Currency Update Warnings</label>
                    <file>currency_update_warning.html</file>
                    <type>text</type>
                </currency_import_error_email_template>
            </email>
        </template>
        <cache>
            <types>
                <config translate="label,description" module="core">
                    <label>Configuration</label>
                    <description>System(config.xml, local.xml) and modules configuration files(config.xml).</description>
                    <tags>CONFIG</tags>
                </config>
                <layout translate="label,description" module="core">
                    <label>Layouts</label>
                    <description>Layout building instructions.</description>
                    <tags>LAYOUT_GENERAL_CACHE_TAG</tags>
                </layout>
            </types>
        </cache>
        <page>
            <layouts>
                <empty module="page" translate="label">
                    <label>Empty</label>
                    <template>page/empty.phtml</template>
                    <layout_handle>page_empty</layout_handle>
                </empty>
                <one_column module="page" translate="label">
                    <label>1 column</label>
                    <template>page/1column.phtml</template>
                    <layout_handle>page_one_column</layout_handle>
                    <is_default>1</is_default>
                </one_column>
            </layouts>
        </page>
        <events>
            <adminhtml_controller_action_predispatch_start>
                <observers>
                    <store>
                        <class>adminhtml/observer</class>
                        <method>bindStore</method>
                    </store>
                    <massaction>
                        <class>adminhtml/observer</class>
                        <method>massactionPrepareKey</method>
                    </massaction>
                </observers>
            </adminhtml_controller_action_predispatch_start>
            <controller_front_init_routers>
                <observers>
                    <cms>
                        <class>Mage_Cms_Controller_Router</class>
                        <method>initControllerRouters</method>
                    </cms>
                </observers>
            </controller_front_init_routers>
        </events>
        <catalog>
            <product>
                <type>
                    <simple translate="label" module="catalog">
                        <label>Simple Product</label>
                        <model>catalog/product_type_simple</model>
                        <composite>0</composite>
                        <index_priority>10</index_priority>
                        <is_qty>1</is_qty>
                    </simple>
                    <grouped translate="label" module="catalog">
                        <label>Grouped Product</label>
                        <model>catalog/product_type_grouped</model>
                        <price_model>catalog/product_type_grouped_price</price_model>
                        <composite>1</composite>
                        <allow_product_types>
                            <simple/>
                            <virtual/>
                            <downloadable/>
                        </allow_product_types>
                        <index_priority>50</index_priority>
                        <price_indexer>catalog/product_indexer_price_grouped</price_indexer>
                        <stock_indexer>cataloginventory/indexer_stock_grouped</stock_indexer>
                    </grouped>
                </type>
            </product>
        </catalog>
    </global>
    <frontend>
        <routers>
            <core>
                <use>standard</use>
                <args>
                    <module>Mage_Core</module>
                    <frontName>core</frontName>
                </args>
            </core>
        </routers>
        <layout>
            <updates>
                <core>
                    <file>core.xml</file>
                </core>
                <page>
                    <file>page.xml</file>
                </page>
            </updates>
        </layout>
    </frontend>
    <adminhtml>
        <acl>
            <resources>
                <admin>
                    <children>
                        <system>
                            <children>
                                <config>
                                    <children>
                                        <moneybookers translate="title" module="moneybookers">
                                            <title>Moneybookers Settings</title>
                                        </moneybookers>
                                    </children>
                                </config>
                            </children>
                        </system>
                    </children>
                </admin>
            </resources>
        </acl>
    </adminhtml>
    <crontab>
        <jobs>
            <core_clean_cache>
                <schedule>
                    <cron_expr>30 2 * * *</cron_expr>
                </schedule>
                <run>
                    <model>core/observer::cleanCache</model>
                </run>
            </core_clean_cache>
        </jobs>
    </crontab>
    <admin>
        <routers>
            <adminhtml>
                <use>admin</use>
                <args>
                    <module>Mage_Adminhtml</module>
                    <modules>
                        <Mage_Index before="Mage_Adminhtml">Mage_Index_Adminhtml</Mage_Index>
                        <Mage_Paygate before="Mage_Adminhtml">Mage_Paygate_Adminhtml</Mage_Paygate>
                        <Mage_Paypal before="Mage_Adminhtml">Mage_Paypal_Adminhtml</Mage_Paypal>
                    </modules>
                    <frontName><![CDATA[admin]]></frontName>
                </args>
            </adminhtml>
        </routers>
    </admin>
</config>
XML;

	protected $movies = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<movies>
	<movie>
		<title>PHP: Behind the Parser</title>
		<characters>
			<character>
				<name>Ms. Coder</name>
				<actor>Onlivia Actora</actor>
			</character>
			<character>
				<name>Mr. Coder</name>
				<actor>El Actor</actor>
			</character>
		</characters>
		<rating type="thumbs">7</rating>
		<rating type="stars">5</rating>
	</movie>
	<movie>
		<title>Magento: Behind the DOM</title>
		<characters>
			<character>
				<name>Mr. XML</name>
				<actor>El DOM</actor>
			</character>
		</characters>
		<rating type="thumbs">8</rating>
		<rating type="stars">4</rating>
	</movie>
</movies>
XML;


	public function setUp()
	{
		//Mage::app('default');
	}



	public function testLoad()
	{
		$xml = $this->configXml;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);
		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());
	}


	public function testToString()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$value1 = $simpleXml->__toString();
		$value2 = $adapterXml->__toString();
		$this->assertEquals($value1, $value2);

		$value1 = $simpleXml->movie->__toString();
		$value2 = $adapterXml->movie->__toString();
		$this->assertEquals($value1, $value2);

		$value1 = $simpleXml->movie->rating->__toString();
		$value2 = $adapterXml->movie->rating->__toString();
		$this->assertEquals($value1, $value2);

		$value1 = $simpleXml->movie->rating['type']->__toString();
		$value2 = $adapterXml->movie->rating['type']->__toString();
		$this->assertEquals($value1, $value2);

	}

	public function testMagicGetLeaf()
	{
		$xml = $this->configXml;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);
		$value1 = (string)$simpleXml->global->models->core->class;
		$value2 = (string)$adapterXml->global->models->core->class;
		$this->assertEquals($value1, $value2);
	}

	public function testMagicGetParent()
	{
		$xml = $this->configXml;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);
		$value1 = $simpleXml->admin;
		$value2 = $adapterXml->admin;
		$this->assertXmlStringEqualsXmlString($value1->asXML(), $value2->asXML());
	}


	public function testMagicGetMultiple()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);
		$value1 = $simpleXml->movie;
		$value2 = $adapterXml->movie;
		$this->assertXmlStringEqualsXmlString($value1->asXML(), $value2->asXML());
	}


	public function testArrayAccess()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$title1 = (string)$simpleXml->movie[0]->title;
		$title2 = (string)$adapterXml->movie[0]->title;
		$this->assertEquals($title1, $title2);

		$title1 = (string)$simpleXml->movie[1]->title;
		$title2 = (string)$adapterXml->movie[1]->title;
		$this->assertEquals($title1, $title2);

	}


	public function testArrayAttribute()
	{
		$xml = $this->configXml;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$value1 = (string)$simpleXml->admin->routers->adminhtml->args->modules->Mage_Index['before'];
		$value2 = (string)$adapterXml->admin->routers->adminhtml->args->modules->Mage_Index['before'];
		$this->assertEquals($value1, $value2);

	}

	public function testCount()
	{
		$xml = $this->configXml;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$value1 = count($simpleXml);
		$value2 = count($adapterXml);
		$this->assertEquals($value1, $value2);

		$value1 = $simpleXml->global->blocks->count();		// 1
		$value2 = $adapterXml->global->blocks->count();
		$this->assertEquals($value1, $value2);

		$value1 = $simpleXml->global->blocks[0]->count();		// 2
		$value2 = $adapterXml->global->blocks[0]->count();
		$this->assertEquals($value1, $value2);

		$value1 = count($simpleXml->global->blocks->core->class);	// 1
		$value2 = count($adapterXml->global->blocks->core->class);
		$this->assertEquals($value1, $value2);

		$value1 = count($simpleXml->global->blocks->core->class[0]);	// 0
		$value2 = count($adapterXml->global->blocks->core->class[0]);
		$this->assertEquals($value1, $value2);

		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$value1 = count($simpleXml->movie);				// 2
		$value2 = count($adapterXml->movie);
		$this->assertEquals($value1, $value2);

	}


	public function testAddChild()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$simpleXml->addChild('name');
		$adapterXml->addChild('name');

		$simpleXml->movie->addChild('name', 'value');
		$adapterXml->movie->addChild('name', 'value');

		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());

	}


	public function testAddAttribute()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$simpleXml->movie->addAttribute('name', 'value');
		$adapterXml->movie->addAttribute('name', 'value');

		// If the attribute already exists, addAttribute does nothing (but it throws a Warning)
		try{
			$simpleXml->movie->rating->addAttribute('type', 'new');
			$adapterXml->movie->rating->addAttribute('type', 'new');
		}catch(Exception $e){

		}

		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());
	}


	public function testGetName()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$value1 = $simpleXml->getName();
		$value2 = $adapterXml->getName();
		$this->assertEquals($value1, $value2);

		$value1 = $simpleXml->movie->getName();
		$value2 = $adapterXml->movie->getName();
		$this->assertEquals($value1, $value2);

		$value1 = $simpleXml->movie->rating['type']->getName();
		$value2 = $adapterXml->movie->rating['type']->getName();
		$this->assertEquals($value1, $value2);

	}


	public function testAttributes()
	{
		$xml = $this->configXml;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

//		<config translate="label,description" module="core">
		$attributes1 = $simpleXml->global->cache->types->config->attributes();
		$attributes2 = $adapterXml->global->cache->types->config->attributes();

		$value1 = $attributes1->count();
		$value2 = $attributes2->count();
		$this->assertEquals($value1, $value2);

		$value1 = count($attributes1);
		$value2 = count($attributes2);
		$this->assertEquals($value1, $value2);

		$value1 = (string)$attributes1;
		$value2 = (string)$attributes2;
		$this->assertEquals($value1, $value2);

		$value1 = (string)$attributes1->translate;
		$value2 = (string)$attributes2->translate;
		$this->assertEquals($value1, $value2);


	}


	public function testForeachAttributes()
	{

		$xml = $this->configXml;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

//		<config translate="label,description" module="core">
		$attributes1 = $simpleXml->global->cache->types->config->attributes();
		$attributes2 = $adapterXml->global->cache->types->config->attributes();

		$value1 = '';
		foreach($attributes1 as $key => $attr){
			$value1 .= $key.$attr->getName().'='.$attr;
		}
		$value2 = '';
		foreach($attributes2 as $key => $attr){
			$value2 .= $key.$attr->getName().'='.$attr;
		}

		$this->assertEquals($value1, $value2);

	}


	public function testForeachElements()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$value1 = '';
		foreach($simpleXml as $key => $value){
			$value1 .= $key.$value->getName();
		}
		$value2 = '';
		foreach($adapterXml as $key => $value){
			$value2 .= $key.$value->getName();
		}
		$this->assertEquals($value1, $value2);


		$value1 = '';
		foreach($simpleXml->movie as $key => $value){
			$value1 .= $key.$value->getName();
		}
		$value2 = '';
		foreach($adapterXml->movie as $key => $value){
			$value2 .= $key.$value->getName();
		}
		$this->assertEquals($value1, $value2);


		$value1 = '';
		foreach($simpleXml->movie[0] as $key => $value){
			$value1 .= $key.$value->getName();
		}
		$value2 = '';
		foreach($adapterXml->movie[0] as $key => $value){
			$value2 .= $key.$value->getName();
		}
		$this->assertEquals($value1, $value2);

	}


	public function testChildren()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$value1 = '';
		foreach($simpleXml->children() as $key => $value){
			$value1 .= $key.$value->getName();
		}
		$value2 = '';
		foreach($adapterXml->children() as $key => $value){
			$value2 .= $key.$value->getName();
		}
		$this->assertEquals($value1, $value2);

		$value1 = '';
		foreach($simpleXml->movie->children() as $key => $value){
			$value1 .= $key.$value->getName();
		}
		$value2 = '';
		foreach($adapterXml->movie->children() as $key => $value){
			$value2 .= $key.$value->getName();
		}
		$this->assertEquals($value1, $value2);

		$value1 = '';
		foreach($simpleXml->movie->title->children() as $key => $value){
			$value1 .= $key.$value->getName();
		}
		$value2 = '';
		foreach($adapterXml->movie->title->children() as $key => $value){
			$value2 .= $key.$value->getName();
		}
		$this->assertEquals($value1, $value2);

	}


	public function testXpath()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		// absolute xpath
		$path = '//name';
		$value1 = '';
		foreach($simpleXml->xpath($path) as $key => $value){
			$value1 .= $key.$value->getName().$value;
		}
		$value2 = '';
		foreach($adapterXml->xpath($path) as $key => $value){
			$value2 .= $key.$value->getName().$value;
		}
		$this->assertEquals($value1, $value2);

		// relative path
		$path = "title";
		$value1 = '';
		foreach($simpleXml->movie->xpath($path) as $key => $value){
			$value1 .= $key.$value->getName().$value;
		}
		$value2 = '';
		foreach($adapterXml->movie->xpath($path) as $key => $value){
			$value2 .= $key.$value->getName().$value;
		}
		$this->assertEquals($value1, $value2);


		$path = "//rating[@type='stars']";
		$value1 = '';
		foreach($simpleXml->movie->title->xpath($path) as $key => $value){
			$value1 .= $key.$value->getName().$value;
		}
		$value2 = '';
		foreach($adapterXml->movie->title->xpath($path) as $key => $value){
			$value2 .= $key.$value->getName().$value;
		}
		$this->assertEquals($value1, $value2);

		// non existent path
		$path = '//notexists';
		$value1 = $simpleXml->xpath($path);
		$value2 = $adapterXml->xpath($path);
		$this->assertSame($value1, $value2);

	}


	public function testAssignAttribute()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$simpleXml->movie->rating->attributes()->type = 'test';
		$adapterXml->movie->rating->attributes()->type = 'test';
		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());

	}


	public function testAssignElement()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$simpleXml->movie->newelement = 'newvalue';
		$adapterXml->movie->newelement = 'newvalue';
		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());

	}

	public function testAssignAttributeWithArrayAccess()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$simpleXml->movie->rating['type'] = 'test';
		$adapterXml->movie->rating['type'] = 'test';
		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());

		// non existent tag (it should create tag fist)
		//$simpleXml->movie->newelement['type'] = 'test';		// <newelement type="test"/></movie>
		$simpleXml->movie->newelement['type'] = 'test';;
		$adapterXml->movie->newelement['type'] = 'test';;
		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());

		$newElement = $simpleXml->movie->newelement;
		$newElement['type2'] = 'test2';
		$newElement = $adapterXml->movie->newelement;
		$newElement['type2'] = 'test2';
		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());

	}

	public function testAssignElementWithArrayAccess()
	{
		$xml = $this->movies;
		$simpleXml = simplexml_load_string($xml);
		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);

		$value = 'newelement';
		$simpleXml->movie[] = $value;		// adds a new <movie>newelement</movie>
		$adapterXml->movie[] = $value;
		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());

		$value = '<newelement attr="value"><child>childvalue</child>newvalue</newelement>';
		$newElement = new SimpleXMLElement($value);
		$simpleXml->movie[] = $newElement;		// adds a new <movie>newvalue</movie>
		$newElement = new SimpleXml_Dom_Element_Adapter($value);
		$adapterXml->movie[] = $newElement;
		$this->assertXmlStringEqualsXmlString($simpleXml->asXML(), $adapterXml->asXML());
	}


	public function testJsonEncode()
	{
//		$xml = $this->movies;
//		$simpleXml = simplexml_load_string($xml);
//		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);
//		$json1 = json_encode($simpleXml);
//		$json2 = json_encode($adapterXml);
	}

	public function testJsonDecode()
	{
//		$xml = $this->movies;
//		$simpleXml = simplexml_load_string($xml);
//		$adapterXml = SimpleXml_Dom_Element_Adapter::loadFromString($xml);
//		$json = json_encode($simpleXml);
//		$array = json_decode($simpleXml,TRUE);
	}





}
