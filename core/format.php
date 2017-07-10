<?php
class format {
	/**
	 * 人民币小写转大写
	 *
	 * @param string $number 数值
	 * @param string $int_unit 币种单位，默认"元"，有的需求可能为"圆"
	 * @param bool $is_round 是否对小数进行四舍五入
	 * @param bool $is_extra_zero 是否对整数部分以0结尾，小数存在的数字附加0,比如1960.30，
	 *             有的系统要求输出"壹仟玖佰陆拾元零叁角"，实际上"壹仟玖佰陆拾元叁角"也是对的
	 * @return string
	 */
	public static function rmb($number = 0, $int_unit = '元', $is_round = TRUE, $is_extra_zero = FALSE) {
		// 将数字切分成两段
		$parts = explode('.', $number, 2);
		$int   = isset($parts[0]) ? strval($parts[0]) : '0';
		$dec   = isset($parts[1]) ? strval($parts[1]) : '';

		// 如果小数点后多于2位，不四舍五入就直接截，否则就处理
		$dec_len = strlen($dec);
		if (isset($parts[1]) && $dec_len > 2) {
			$dec = $is_round
			? substr(strrchr(strval(round(floatval("0." . $dec), 2)), '.'), 1)
			: substr($parts[1], 0, 2);
		}

		// 当number为0.001时，小数点后的金额为0元
		if (empty($int) && empty($dec)) {
			return '零';
		}

		// 定义
		$chs     = array('0', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖');
		$uni     = array('', '拾', '佰', '仟');
		$dec_uni = array('角', '分');
		$exp     = array('', '万');
		$res     = '';

		// 整数部分从右向左找
		for ($i = strlen($int) - 1, $k = 0; $i >= 0; $k++) {
			$str = '';
			// 按照中文读写习惯，每4个字为一段进行转化，i一直在减
			for ($j = 0; $j < 4 && $i >= 0; $j++, $i--) {
				$u   = $int{$i} > 0 ? $uni[$j] : ''; // 非0的数字后面添加单位
				$str = $chs[$int{$i}] . $u . $str;
			}
			//echo $str."|".($k - 2)."<br>";
			$str = rtrim($str, '0'); // 去掉末尾的0
			$str = preg_replace("/0+/", "零", $str); // 替换多个连续的0
			if (!isset($exp[$k])) {
				$exp[$k] = $exp[$k - 2] . '亿'; // 构建单位
			}
			$u2  = $str != '' ? $exp[$k] : '';
			$res = $str . $u2 . $res;
		}

		// 如果小数部分处理完之后是00，需要处理下
		$dec = rtrim($dec, '0');

		// 小数部分从左向右找
		if (!empty($dec)) {
			$res .= $int_unit;

			// 是否要在整数部分以0结尾的数字后附加0，有的系统有这要求
			if ($is_extra_zero) {
				if (substr($int, -1) === '0') {
					$res .= '零';
				}
			}

			for ($i = 0, $cnt = strlen($dec); $i < $cnt; $i++) {
				$u = $dec{$i} > 0 ? $dec_uni[$i] : ''; // 非0的数字后面添加单位
				$res .= $chs[$dec{$i}] . $u;
			}
			$res = rtrim($res, '0'); // 去掉末尾的0
			$res = preg_replace("/0+/", "零", $res); // 替换多个连续的0
		} else {
			$res .= $int_unit . '整';
		}
		return $res;
	}

	/**
	 * 格式化容量
	 *
	 * @param   int  $size    bytes
	 * @return string
	 */
	public static function size($size) {
		$unit = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($f = 0; $size >= 1024 && $f < 4; $f++) {
			$size /= 1024;
		}
		return round($size, 2) . $unit[$f];
	}

	/**
	 * 格式化时间
	 *
	 * @param   int  $time  timestamp时间戳
	 * @return string
	 */
	public static function ago($time) {
		$dur = $_SERVER['REQUEST_TIME'] - $time;
		if ($dur < 60) {
			return $dur . '秒前';
		}

		if ($dur < 3600) {
			return floor($dur / 60) . '分钟前';
		}

		if ($dur < 86400) {
			return floor($dur / 3600) . '小时前';
		}

		if ($dur < 259200) {
			return floor($dur / 86400) . '天前';
		}

		return date('Y-m-d H:i', $time);
	}

	/**
	 * 汉子转拼音
	 *
	 * @param  string  $content  汉子
	 * @param  string  $separated  分割符号
	 * @param  string  $charset  编码
	 * @return string
	 */
	public static function pinyin($content, $separated = '', $charset = 'utf-8') {
		function pinyinTranslate($number, $data) {
			if ($number > 0 && $number < 160) {
				return chr($number);
			} elseif ($number < -20319 || $number > -10247) {
				return '';
			} else {
				foreach ($data as $k => $v) {
					if ($v <= $number) {
						break;
					}

				}
				return $k;
			}
		}

		function pinyinConvert($content) {
			$string = '';
			if ($content < 0x80) {
				$string .= $content;
			} elseif ($content < 0x800) {
				$string .= chr(0xC0 | $content >> 6);
				$string .= chr(0x80 | $content & 0x3F);
			} elseif ($content < 0x10000) {
				$string .= chr(0xE0 | $content >> 12);
				$string .= chr(0x80 | $content >> 6 & 0x3F);
				$string .= chr(0x80 | $content & 0x3F);
			} elseif ($content < 0x200000) {
				$string .= chr(0xF0 | $content >> 18);
				$string .= chr(0x80 | $content >> 12 & 0x3F);
				$string .= chr(0x80 | $content >> 6 & 0x3F);
				$string .= chr(0x80 | $content & 0x3F);
			}
			return iconv('UTF-8', 'GB2312', $string);
		}
		$data = '';
		$k    = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
		$v    = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274|-10270|-10262|-10260|-10256|-10254";
		$kk   = explode('|', $k);
		$vv   = explode('|', $v);
		$data = array_combine($kk, $vv);
		arsort($data);
		reset($data);
		if ($charset != 'gb2312') {
			$content = pinyinConvert($content);
		}

		$result = '';
		$array  = array();
		for ($i = 0; $i < strlen($content); $i++) {
			$p = ord(substr($content, $i, 1));
			if ($p > 160) {
				$q = ord(substr($content, ++$i, 1));
				$p = $p * 256 + $q - 65536;
			}
			//$result.=pinyin_translate($p, $data);
			$array[] = pinyinTranslate($p, $data);
		}
		$result = (string) implode($separated, $array);
		return $result;
		#return preg_replace("/[^a-z0-9]*/",'',$result);
	}

	public static function markdown($content) {

	}

}