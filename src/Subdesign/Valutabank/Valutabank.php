<?php

namespace Subdesign\Valutabank;

use Cache;
use anlutro\cURL\cURL;

/**
 * Valutabank.hu api wrapper for Laravel 5.
 *
 * @author Barna Szalai <szalai.b@gmail.com>
 */
class Valutabank
{
    /**
     * currencies.
     *
     * @var string|array
     */
    protected $currencies;

    /**
     * retruntype.
     *
     * @var array|html
     */
    protected $retruntype;

    /**
     * show bank name.
     *
     * @var bool
     */
    protected $show_bank;

    /**
     * source url.
     *
     * @var string
     */
    protected $source_url = 'http://www.valutabank.hu/rss/valutabank.xml';

    /**
     * use curl.
     *
     * @var bool
     */
    protected $curl;

    /**
     * cURL object.
     *
     * @var object
     */
    protected $oCurl = null;

    /**
     * caching.
     *
     * @var bool
     */
    protected $cache;

    /**
     * cache time to leave value (sec).
     *
     * @var int
     */
    protected $cache_ttl;

    /**
     * icon path.
     *
     * @var string
     */
    protected $icon_path;

    /**
     * icon name.
     *
     * @var string
     */
    protected $icon_name;

    /**
     * icon extension.
     *
     * @var string
     */
    protected $icon_ext;

    public function __construct()
    {
        $config = config('valutabank');

        if (! $config) {
            throw new \Exception('Config file valutabank.php not found!');
        }

        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }

        if ($this->curl) {
            $this->oCurl = new cURL();
        }
    }

    public function get()
    {
        if ($this->cache) {
            if (empty($this->cache_ttl)) {
                throw new \Exception('You have to set an integer value for cache_ttl in the config');
            }

            $sXml = Cache::remember('xml', $this->cache_ttl, function () {
                return $this->getXml();
            });
        } else {
            $sXml = $this->getXml();
        }

        $oXml = simplexml_load_string($sXml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $lastUpdate = date('Y-m-d H:i:s', strtotime($oXml->channel->pubDate));

        $result = null;

        if ($this->returntype == 'html') {
            $result = $this->renderHtml($oXml->channel->item, $lastUpdate);
        } elseif ($this->returntype == 'array' || $this->returntype == 'json') {
            $result = $this->renderArray($oXml->channel->item, $lastUpdate);
        } else {
            throw new \Exception("Error in config settings, return type isn't set correctly.");
        }

        return $result;
    }

    private function getXml()
    {
        if (is_object($this->oCurl)) {
            $content = $this->oCurl->get($this->source_url);
        } else {
            $content = file_get_contents($this->source_url);
        }

        return $content;
    }

    private function renderHtml($data, $lastUpdate)
    {
        $html = '';

        $html .= '<table align="center"><thead><th></th><th>'.trans('valutabank::valutabank.buying_rate').'</th><th>'.trans('valutabank::valutabank.selling_rate').'</th></thead><tbody>';

        if (is_string($this->currencies) && $this->currencies === 'all') {
            foreach ($data as $key => $value) {
                preg_match_all('/(\d+).(\d+)/', $value->description, $matches);

                preg_match_all('/\(([A-Za-z0-9 ]+?)\)/', $value->description, $bank_name);

                $html .= '<tr>';
                $html .= '<td align="left" width="90">'.substr($value->title, 0, 3).'&nbsp;<img src="'.$this->icon_path.$this->icon_name.'-'.strtolower(substr($value->title, 0, 3)).'.'.$this->icon_ext.'" border="0"/></td><td align="right">'.$matches[0][0].' '.trans('valutabank::valutabank.ft').($this->show_bank ? ' '.$bank_name[0][0] : '').'</td><td align="right">'.$matches[0][1].' '.trans('valutabank::valutabank.ft').($this->show_bank ? ' '.$bank_name[0][1] : '').'</td>';
                $html .= '</tr>';
            }
        } elseif (is_array($this->currencies) && count($this->currencies)) {
            foreach ($data as $key => $value) {
                foreach ($this->currencies as $currency) {
                    if (substr($value->title, 0, 3) == $currency) {
                        preg_match_all('/(\d+).(\d+)/', $value->description, $matches);

                        preg_match_all('/\(([A-Za-z0-9 ]+?)\)/', $value->description, $bank_name);

                        $html .= '<tr>';
                        $html .= '<td align="left" width="90">'.$currency.'&nbsp;<img src="'.$this->icon_path.$this->icon_name.'-'.strtolower($currency).'.'.$this->icon_ext.'" border="0"/></td><td align="right">'.$matches[0][0].' '.trans('valutabank::valutabank.ft').($this->show_bank ? ' '.$bank_name[0][0] : '').'</td><td align="right">'.$matches[0][1].' '.trans('valutabank::valutabank.ft').($this->show_bank ? ' '.$bank_name[0][1] : '').'</td>';
                        $html .= '</tr>';

                        $this->currencies = array_diff($this->currencies, array($currency));
                    }
                }

                if (! count($this->currencies)) {
                    break;
                }
            }
        } else {
            throw new \Exception("Error in config settings, currencies aren't set correctly.");
        }

        $html .= '<tr><td colspan="3" align="center"><span>'.trans('valutabank::valutabank.last_updated').' '.$lastUpdate.'<br/><span style="font-size:9px;">'.trans('valutabank::valutabank.source').'</span></span></td></tr>';
        $html .= '</tbody></table>';

        return $html;
    }

    private function renderArray($data, $lastUpdate)
    {
        $array = [];

        $array['lastUpdate'] = $lastUpdate;

        if (is_string($this->currencies) && $this->currencies === 'all') {
            foreach ($data as $key => $value) {
                preg_match_all('/(\d+).(\d+)/', $value->description, $matches);

                preg_match_all('/\(([A-Za-z0-9 ]+?)\)/', $value->description, $bank_name);

                $currency = substr($value->title, 0, 3);

                $array[$currency] = [
                    'buying_rate'  => [
                        'value' => $matches[0][0],
                        'bank'  => $bank_name[1][0],
                    ],
                    'selling_rate' => [
                        'value' => $matches[0][1],
                        'bank'  => $bank_name[1][1],
                    ],
                    'icon' => $this->icon_path.$this->icon_name.'-'.strtolower($currency).'.'.$this->icon_ext,
                ];
            }
        } elseif (is_array($this->currencies) && count($this->currencies)) {
            foreach ($data as $key => $value) {
                foreach ($this->currencies as $currency) {
                    if (substr($value->title, 0, 3) == $currency) {
                        preg_match_all('/(\d+).(\d+)/', $value->description, $matches);

                        preg_match_all('/\(([A-Za-z0-9 ]+?)\)/', $value->description, $bank_name);

                        $array[$currency] = [
                            'buying_rate'  => [
                                'value' => $matches[0][0],
                                'bank'  => $bank_name[1][0],
                            ],
                            'selling_rate' => [
                                'value' => $matches[0][1],
                                'bank'  => $bank_name[1][1],
                            ],
                            'icon' => $this->icon_path.$this->icon_name.'-'.strtolower($currency).'.'.$this->icon_ext,
                        ];

                        $this->currencies = array_diff($this->currencies, array($currency));
                    }
                }
                // if currencies run out, don't continue foreach loop
                if (! count($this->currencies)) {
                    break;
                }
            }
        } else {
            throw new \Exception("Error in config settings, currencies aren't set correctly.");
        }

        if ($this->returntype == 'json') {
            $array = json_encode($array, JSON_PRETTY_PRINT);
        }

        return $array;
    }
}