<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoQtiItemRestApi\test\v1;


use core_kernel_classes_Resource;
use oat\taoRestAPI\test\TaoCurlRequest\RestTestCase;
use tao_helpers_Uri;
use tao_models_classes_ClassService;
use taoItems_models_classes_ItemsService;

class QtiItemRestApiTest extends RestTestCase
{

    /**
     * @var string
     */
    protected $uri = 'taoQtiItemRestApi/v1/';

    /**
     * @var tao_models_classes_ClassService
     */
    protected $service = null;

    /**
     * Test items
     *
     * @var array of the itemUris
     */
    private $items = [];

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        \common_ext_ExtensionsManager::singleton()->getExtensionById('taoItemRestApi');
        $this->service = taoItems_models_classes_ItemsService::singleton();
    }

    public function serviceProvider()
    {
        return [[$this->uri, $this->service->getRootClass()]];
    }

    public function tearDown()
    {
        parent::tearDown();
        foreach ($this->items as $itemUri) {
            $this->service->deleteResource(new core_kernel_classes_Resource($itemUri));
        }
    }

    /**
     * Test Http POST, GET, DELETE requests
     */
    public function testPostAndGetAndDelete()
    {
        // create
        $data = $this->checkPost($this->uri);

        self::assertNotFalse($data);

        $this->items[] = $uriResource = $data[0]['uri'];
        $resource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uriResource));
        $this->assertTrue($resource->exists(), 'Object should be exists');

        // get
        $this->checkGet($this->uri . '?uri=' . urlencode($uriResource), $this->service->getRootClass());

        // delete
        $this->checkDelete($this->uri . '?uri=' . urlencode($uriResource));
        $this->assertFalse($resource->exists(), 'Object should be deleted');

        // check deletion from storage
        $resource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($uriResource));
        $this->assertFalse($resource->exists(), 'Object should be deleted');
    }
}
