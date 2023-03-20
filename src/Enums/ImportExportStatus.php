<?php

namespace Thomascombe\BackpackAsyncExport\Enums;

enum ImportExportStatus: string
{
    case Created = 'created';
    case Processing = 'processing';
    case Successful = 'successful';
    case Error = 'error';
    case Deleted = 'deleted';
}
