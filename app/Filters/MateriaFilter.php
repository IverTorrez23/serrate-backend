<?php
namespace App\Filters;
use Illuminate\Http\Request;
use App\Filters\ApiFilter;

class MateriaFilter extends ApiFilter{
    protected $safeParamas=[
        'nombre'=>['eq'],
        'abreviatura'=>['eq'],
    ];
    protected $columnMap=[];

    protected $operatorMap=[
        'eq'=>'=',
        'lt'=>'<',
        'lte'=>'<=',
        'gt'=>'>',
        'gte'=>'>='
    ];

}
