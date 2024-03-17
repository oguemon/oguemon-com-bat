<?php
// composer読み込み
require_once './vendor/autoload.php';

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;

/** Google Analyticsから特定条件の人気記事のパスを取得する */
function get_ranking_paths(
    string $key_location,
    string $property_id,
    string $get_count,
)
{
    $client = new BetaAnalyticsDataClient(['credentials' => $key_location]);
    $response = $client->runReport([
        'property' => 'properties/' . $property_id,
        'dateRanges' => [
            new DateRange([
                'start_date' => '3daysAgo',
                'end_date'   => 'today',
            ]),
        ],
        // フィルタリング用にデータの属性を指定します。
        'dimensions' => [
            new Dimension([
                'name' => 'pagePath',
            ]),
        ],
        'dimensionFilter' => new FilterExpression([
            'not_expression' => new FilterExpression([
                'filter' => new Filter([
                    'field_name' => 'pagePath',
                    'string_filter' => new StringFilter([
                        'match_type' => MatchType::PARTIAL_REGEXP,
                        // ランキングの妙味を高めるために次のページを除外する
                        // ^/$              : トップページ
                        // ^/[^/]+/$        : 非記事ページ（/about/など）
                        // ^/topic/         : アーカイブトップページ
                        // /linear-algebra/ : 線形代数関連記事
                        'value' => '^/$|^/[^/]+/$|^/topic/|/linear-algebra/',
                    ]),
                ]),
            ])
        ]),
        "limit" => $get_count,
        'metrics' => [
            new Metric(['name' => 'screenPageViews']),
        ],
        'orderBys' => [
            new OrderBy([
                'metric' => new OrderBy\MetricOrderBy([
                    'metric_name' => 'screenPageViews',
                ]),
                'desc'   => true,
            ]),
        ],
    ]);

    $paths = [];
    foreach ($response->getRows() as $row) {
        $paths[] = $row->getDimensionValues()[0]->getValue();
    }

    $ret = [
        'datetime' => date('c'),
        'paths' => $paths
    ];
    print_r($ret);
    return $ret;
}
