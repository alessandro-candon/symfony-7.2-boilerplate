<?php

namespace App\Order;

use App\Exception\System\BadOrderParameterException;
use App\Utils\ArrayUtils;
use Doctrine\ORM\QueryBuilder;

use function Symfony\Component\String\u;

final class OrderApplier
{
    protected array $orderAppliers;

    /**
     * @param array<string, mixed> $data -> query parameters sent by url order[id]=ASC,order[name]=DESC
     */
    public function __construct(
        private readonly array $allowedOrderAppliers,
        readonly array $data
    ) {
        $this->orderAppliers = ArrayUtils::get('order', $data, []);
    }

    /**
     * @throws BadOrderParameterException
     */
    public function apply(QueryBuilder $target): void
    {
        foreach ($this->orderAppliers as $sortBy => $order) {
            $order = strtoupper($order);
            if (! in_array($sortBy, $this->allowedOrderAppliers)) {
                throw new BadOrderParameterException('This order key is not allowed for this API');
            }

            if ($order !==  'ASC' && $order !== 'DESC') {
                throw new BadOrderParameterException('This order value is not allowed, use "ASC" or "DESC"');
            }
            $sortBy = implode(
                '.',
                [
                    explode('.', $sortBy)[0],
                    u(explode('.', $sortBy)[1])->camel()->toString()
                ]
            );
            $target->orderBy($sortBy, $order);
        }
    }
}
