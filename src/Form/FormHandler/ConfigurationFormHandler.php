<?php

namespace IdealoFeed\Form\FormHandler;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Tools;

class ConfigurationFormHandler
{

    private Configuration $configuration;

    public function __construct(
        Configuration $configuration,
    ) {
        $this->configuration = $configuration;
    }

    /**
     * Salva i dati del form nelle configurazioni PrestaShop
     *
     * @param array $data
     */
    public function save(array $data): void
    {
        // 🔹 Qui mappi i campi del form con le costanti di configurazione del modulo
        // esempio: se il tuo form ha un campo 'enabled', salvalo come 'IdealoFeed_ENABLED'
        foreach ($data as $key => $value) {
            $configKey = strtoupper($key);
            $this->configuration->set($configKey, $value);
        }
    }

    /**
     * Recupera i valori correnti da Configuration (usato per popolare il form)
     *
     * @return array
     */
    public function getData(): array
    {
        // For checkbox fields convert string "1"/"0" to boolean for Symfony form
        $data['LIVE_URL'] = Tools::getHttpHost(true)
            . __PS_BASE_URI__
            . "module/idealofeed/crono";


        return $data;
    }
}
