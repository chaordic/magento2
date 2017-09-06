<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App\PageCache;

/**
 * Page unique identifier
 */
class Identifier
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Cache
     */
    protected $cache;

     /**
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Http\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Cache $cache
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Http\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Cache $cache
    ) {
        $this->request = $request;
        $this->context = $context;
        $this->scopeConfig = $scopeConfig;
        $this->cache = $cache;
    }

    /**
     * Return unique page identifier
     *
     * @return string
     */
    public function getValue()
    {
        $url = $this->cache->load('WEB_SECURE_URL');

        if(empty($url)) {
            $url = $this->scopeConfig->getValue('web/secure/base_url', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->cache->save($url,'WEB_SECURE_URL');
        }

        $data = [
            $this->request->isSecure(),
            $url,
            $this->request->getFrontName(),
            $this->request->get(\Magento\Framework\App\Response\Http::COOKIE_VARY_STRING)
                ?: $this->context->getVaryString()
        ];
        return md5(serialize($data));
    }
}
