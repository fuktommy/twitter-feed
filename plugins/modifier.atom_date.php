<?php
function smarty_modifier_atom_date($datetimeString)
{
    return date(DATE_ATOM, strtotime($datetimeString));
}
