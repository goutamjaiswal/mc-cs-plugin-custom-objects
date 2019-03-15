<?php

declare(strict_types=1);

/*
 * @copyright   2019 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.com
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\CustomObjectsBundle\Provider;

class CustomItemSessionProvider extends StandardSessionProvider
{
    public const KEY_PAGE = 'custom.item.page';

    public const KEY_LIMIT = 'mautic.custom.item.limit';

    public const KEY_ORDER_BY = 'mautic.custom.item.orderby';

    public const KEY_ORDER_BY_DIR = 'mautic.custom.item.orderbydir';

    public const KEY_FILTER = 'mautic.custom.item.filter';
}