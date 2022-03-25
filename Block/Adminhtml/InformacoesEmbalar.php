<?php
/**
 * Copyright © Marcelo Caetano All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Funarbe\InformacoesEmbalar\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class InformacoesEmbalar extends Template
{
    private \Magento\Framework\App\ResourceConnection $_resource;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        Context $context,
        array $data = []
    ) {
        $this->_resource = $resource;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function informacoesEmbalar(): string
    {
        $connection = $this->_resource->getConnection();
        $orderId = $this->getRequest()->getParam('order_id');
        $sales_order = $connection->getTableName('sales_order');

        $query = "SELECT
                  JSON_EXTRACT(info_embalar, '$.caixa') AS caixas,
                  JSON_EXTRACT(info_embalar, '$.congelado') AS congelados,
                  JSON_EXTRACT(info_embalar, '$.bolsa') AS bolsas,
                  JSON_EXTRACT(info_embalar, '$.observacao') AS observacao
                FROM $sales_order
                WHERE entity_id = $orderId";

        $result = $connection->fetchAll($query);

        $caixas = str_replace("\"", "", $result[0]['caixas']);
        $congelados = str_replace("\"", "", $result[0]['congelados']);
        $bolsas = str_replace("\"", "", $result[0]['bolsas']);
        $observacao = str_replace("\"", "", $result[0]['observacao']);

        if (empty($caixas)) {
            return "<section class='admin__page-section'>
                <div class='admin__page-section-title'>
                    <span class='title'>Opções de embalagem na expedição</span>
                </div>
                <div class='admin__page-section-content'>
                    <div class='admin__page-section-item-content'>
                    <div class='messages'>
                        <div class='message message-warning message-demo-mode'>
                            As informações só aparecem quando as compras forem expedidas.
                        </div>
                    </div>
                    </div>
                </div>
            </section>";
        }

        return "<section class='admin__page-section'>
                <div class='admin__page-section-title'>
                    <span class='title'>Opções de embalagem na expedição</span>
                </div>
                <div class='admin__page-section-content'>
                    <div class='admin__page-section-item-content'>
                        <strong>Caixas:</strong> " . $caixas . " <br>
                        <strong>Bolsas:</strong> " . $bolsas . " <br>
                        <strong>Congelados:</strong> " . $congelados . " <br>
                        <strong>Observações:</strong> " . $observacao . "
                    </div>
                </div>
            </section>";
    }
}
