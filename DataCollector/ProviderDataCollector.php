<?php

namespace Mremi\UrlShortenerBundle\DataCollector;

use Mremi\UrlShortener\Provider\ChainProvider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

/**
 * Provider data collector class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class ProviderDataCollector implements DataCollectorInterface, \Serializable
{
    /**
     * @var ChainProvider
     */
    private $chainProvider;

    /**
     * @var array
     */
    private $providers = array();

    /**
     * Constructor
     *
     * @param ChainProvider $chainProvider A chain provider instance
     */
    public function __construct(ChainProvider $chainProvider)
    {
        $this->chainProvider = $chainProvider;
    }

    /**
     * Gets the configured providers
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        foreach ($this->chainProvider->getProviders() as $provider) {
            $this->providers[$provider->getName()] = $provider->getTraces();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            'providers' => $this->providers,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $unserialized = unserialize($data);

        $this->providers = $unserialized['providers'];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'url_shortener';
    }
}