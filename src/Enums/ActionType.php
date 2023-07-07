<?php

namespace Thomascombe\BackpackAsyncExport\Enums;

enum ActionType: string
{
    case Import = 'import';
    case Export = 'export';
}
