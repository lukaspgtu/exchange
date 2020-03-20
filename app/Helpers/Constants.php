<?php

# ORDER TYPE AND PLATFORM MARKET TYPE

defined('BUY') OR define('BUY', 1);

defined('SALE') OR define('SALE', 2);

defined('BUY_FEE') OR define('BUY_FEE', 3);

defined('SALE_FEE') OR define('SALE_FEE', 4);

# ACCOUNT TYPE

defined('FISICAL') OR define('FISICAL', 1);

defined('LEGAL') OR define('LEGAL', 2);

defined('FOREIGN') OR define('FOREIGN', 3);

# STATUS

defined('WAITING') OR define('WAITING', 0);

defined('CONFIRMED') OR define('CONFIRMED', 1);

defined('CANCELED') OR define('CANCELED', 2);

defined('RUNNING') OR define('RUNNING', 3);

# GAIN

defined('GAIN_ORDER') OR define('GAIN_ORDER', 1);

defined('GAIN_TICKER') OR define('GAIN_TICKER', 2);


# DESCRIPTION

defined('BUY_DESCRIPTION') OR define('BUY_DESCRIPTION', 'Compra');

defined('SALE_DESCRIPTION') OR define('SALE_DESCRIPTION', 'Venda');

defined('BUY_FEE_DESCRIPTION') OR define('BUY_FEE_DESCRIPTION', 'Taxa sobre compra');

defined('SALE_FEE_DESCRIPTION') OR define('SALE_FEE_DESCRIPTION', 'Taxa sobre venda');

# DEPOSIT TYPE

defined('DEPOSIT_SOPAGUE') OR define('DEPOSIT_SOPAGUE', 1);

defined('DEPOSIT_BITCOIN') OR define('DEPOSIT_BITCOIN', 2);
