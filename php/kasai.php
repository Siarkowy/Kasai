<?php

// Kasai to SQL PHP-driven translator <https://github.com/Siarkowy/Kasai>
// Copyright by Siarkowy, 2014. Released under the terms of BSD 2-Clause license.

class Kasai
{
    static $events = array(
        'timerooc'              => 1,
        'timer'                 => 0,
        'health'                => 2,
        'mana'                  => 3,
        'aggro'                 => 4,
        'killed'                => 5,
        'died'                  => 6,
        'evade'                 => 7,
        'spellhit'              => 8,
        'range'                 => 9,
        'ooclos'                => 10,
        'spawned'               => 11,
        'targethp'              => 12,
        'targetcasting'         => 13,
        'friendlyhp'            => 14,
        'friendlyiscc'          => 15,
        'friendlymissingbuff'   => 16,
        'summonedunit'          => 17,
        'targetmana'            => 18,
        'questaccept'           => 19,
        'questcomplete'         => 20,
        'reachedhome'           => 21,
        'receivedemote'         => 22,
        'buffed'                => 23,
        'targetbuffed'          => 24,
        'reset'                 => 35,
    );

    static $flags = array(
        'repeatable'            => 0x01,
        'normal'                => 0x02,
        'heroic'                => 0x04,
        'debug'                 => 0x80,
    );

    static $unitflags = array(
        'nonattackable1'        => 0x00000080,
        'nonattackable2'        => 0x00000100,
        // above have to be earlier
        'nonattackable'         => 0x00000002,
        'disablemove'           => 0x00000004,
        'pvpattackable'         => 0x00000008,
        'preparation'           => 0x00000020,
        'passive'               => 0x00000200,
        'looting'               => 0x00000400,
        'petincombat'           => 0x00000800,
        'pvp'                   => 0x00001000,
        'silenced'              => 0x00002000,
        'nonplspelltarget'      => 0x00010000,
        'pacified'              => 0x00020000,
        'disablerotate'         => 0x00040000,
        'incombat'              => 0x00080000,
        'taxiflight'            => 0x00100000,
        'disarmed'              => 0x00200000,
        'confused'              => 0x00400000,
        'fleeing'               => 0x00800000,
        'playercontrolled'      => 0x01000000,
        'notselectable'         => 0x02000000,
        'skinnable'             => 0x04000000,
        'mounted'               => 0x08000000,
        'sheathe'               => 0x40000000,
    );

    static $actions = array(
        'none'                  => 0,
        'text'                  => 1,
        'setfaction'            => 2,
        'morph'                 => 3,
        'sound'                 => 4,
        'emote'                 => 5,
        'randomsay'             => 6,
        'sayrandom'             => 6,
        'randomyell'            => 7,
        'yellrandom'            => 7,
        'randomtextemote'       => 8,
        'randomsound'           => 9,
        'randomemote'           => 10,
        'cast'                  => 11,
        'summon'                => 12,
        'threatsinglepct'       => 13,
        'threatallpct'          => 14,
        'questevent'            => 15,
        'castevent'             => 16,
        'setunitfield'          => 17,
        'setflag'               => 18,
        'removeflag'            => 19,
        'autoattcak'            => 20,
        'combatmovement'        => 21,
        'setphase'              => 22,
        'incphase'              => 23,
        'goevade'               => 24,
        'fleeforassist'         => 25,
        'questeventall'         => 26,
        'casteventall'          => 27,
        'removeauras'           => 28,
        'rangedmovement'        => 29,
        'randomphase'           => 30,
        'randomphaserange'      => 31,
        'summonid'              => 32,
        'killedmonster'         => 33,
        'setinstdata'           => 34,
        'setinstdata64'         => 35,
        'updatetemplate'        => 36,
        'die'                   => 37,
        'docombatpulse'         => 38,
        'callforhelp'           => 39,
        'setsheath'             => 40,
        'despawn'               => 41,
        'setinvincibility'      => 42,
        'removecorpse'          => 43,
        'castguid'              => 44,
        'stopcombat'            => 45,
        'checkthreat'           => 46,

        'setphasemask'          => 97,
        'setstandstate'         => 98,
        'moverandompoint'       => 99,
        'setvisibility'         => 100,
        'setactive'             => 101,
        'setaggressive'         => 102,
        'attackstartpulse'      => 103,
        'summongo'              => 104,
    );

    static $keywords = array(
        'self'                  => 0,
        'highestaggro'          => 1,
        'secondaggro'           => 2,
        'lastaggro'             => 3,
        'random'                => 4,
        'randomnottop'          => 5,
        'invoker'               => 6,
        'highestaggropets'      => 7,
        'secondaggropets'       => 8,
        'lastaggropets'         => 9,
        'randompets'            => 10,
        'randomnottoppets'      => 11,
        'invokernotplayer'      => 12,
        'null'                  => 13,

        'interrupt'             => 0x01,
        'triggered'             => 0x02,
        'forced'                => 0x04,
        'nomeleeoom'            => 0x08,
        'targetself'            => 0x10,
        'noaura'                => 0x20,
    );

    static function sqlify($kai)
    {
        $npc        = 0;    // npc ID
        $type       = -1;   // event type
        $mask       = 0;    // event inverse phase mask
        $chance     = 100;  // event chance
        $flags      = 0;    // event flags
        $event      = array(0, 0, 0, 0); // par1 par2 par3 par4
        $action     = array(
            array(0, 0, 0, 0), // action1 par1 par2 par3
            array(0, 0, 0, 0), // action2 par1 par2 par3
            array(0, 0, 0, 0), // action3 par1 par2 par3
        );
        $comment    = '';   // contains npc name if specified

        $actionid   = 0;
        $npc_block  = false;

        $entry = explode(' ', $kai);

        while ($d = array_shift($entry))
            switch ($d)
            {
                case 'npc':
                    if (!is_numeric($d = array_shift($entry)))
                        array_unshift($entry, $d);
                    $npc = (int) $d;
                    while ($d = array_shift($entry))
                        if ($d{0} == strtoupper($d{0}))
                            $comment .= $d . ' ';
                        else
                        {
                            array_unshift($entry, $d);
                            break;
                        }
                    break;

                case 'event':
                    if (!is_numeric($d = array_shift($entry)))
                    {
                        array_unshift($entry, $d);
                        break;
                    }
                    $type = (int) $d;
                    $eventpar = 0;
                    while (is_numeric($par = array_shift($entry)) && $eventpar < 4)
                        $event[$eventpar++] = (float) $par;
                    array_unshift($entry, $par);
                    break;

                case 'chance':
                    if (!is_numeric($d = array_shift($entry)))
                    {
                        array_unshift($entry, $d);
                        break;
                    }
                    $chance = (int) $d;
                    break;

                case 'notphase':
                    if (!is_numeric($d = array_shift($entry)))
                    {
                        array_unshift($entry, $d);
                        break;
                    }
                    $mask = (int) $d;
                    break;

                case 'action':
                    if (!is_numeric($d = array_shift($entry)))
                    {
                        array_unshift($entry, $d);
                        break;
                    }
                    $actionpar = 0;
                    $action[$actionid][$actionpar++] = (int) $d;
                    while (is_numeric($par = array_shift($entry)) && $actionpar < 4)
                        $action[$actionid][$actionpar++] = (float) $par;
                    ++$actionid;
                    array_unshift($entry, $par);
                    break;

                case 'flag':
                    if (!is_numeric($d = array_shift($entry)))
                    {
                        array_unshift($entry, $d);
                        break;
                    }
                    $flags |= (int) $d;
                    break;

                default:
                    throw new Exception(sprintf('[Kasai] Error: %s in `%s`.',
                        is_numeric($d) ? "Too many numeric arguments supplied near `{$d}`" :
                            "Unknown keyword `{$d}`",
                        $kai
                    ));
                    return;
            }

        // handle comment

        $comment = array(
            trim($comment == '' ? 'Unknown' : $comment),
            array_search($type, Kasai::$events)
        );

        foreach ($action as $data)
            if ($data[0])
                $comment[] = array_search($data[0], Kasai::$actions) . ' ' . $data[1];

        if ($chance != 100)
            $comment[] = "chance {$chance}%";

        if ($mask)
            $comment[] = "notphase {$mask}";

        foreach (Kasai::$flags as $name => $flag)
            if ($flags & $flag)
                $comment[] = $name;

        if (!$npc)
            throw new Exception("[Kasai] Error: Missing NPC identifier in `{$kai}`.");

        if ($type < 0)
            throw new Exception("[Kasai] Error: Missing event type in `{$kai}`.");

        if (!($flags & 0x06))   // neither normal nor heroic
            $flags |= 0x06;     // set both

        return '(' . implode(', ', array(
            'null', $npc, $type, $mask, $chance, $flags,
            implode(', ', $event),
            implode(', ', $action[0]),
            implode(', ', $action[1]),
            implode(', ', $action[2]),
            "'" . implode(' - ', $comment) . "'"
        )) . ')';
    }

    static function flatten($prefix, $arr, $result)
    {
        foreach ($arr as $outer => $inner)
            if (is_array($inner))
                Kasai::flatten(trim($prefix . ' ' . $outer), $inner, &$result);
            else
                if ($sql = Kasai::sqlify($prefix . ' ' . $inner))
                    $result[] = $sql;
    }

    static function expand($tree, $kai)
    {
        $hasbraces = false;
        $outer_start = 0;
        $count = 0;

        for ($i = 0; $i < strlen($kai); ++$i)
        {
            if ($kai{$i} == '{')
            {
                $hasbraces = true;
                if ($count++ == 0)
                {
                    $outer = trim(substr($kai, $outer_start, $i - $outer_start));
                    $inner_start = $i + 1;
                }
            }
            elseif ($kai{$i} == '}')
            {
                if (--$count == 0)
                {
                    $inner = trim(substr($kai, $inner_start, $i - $inner_start));
                    $tree[$outer] = array();
                    Kasai::expand(&$tree[$outer], $inner);
                    $outer_start = $i + 1;
                }
            }
        }

        if (!$hasbraces)
            $tree[] = $kai;
    }

    static function parse($kai, $table = null)
    {
        if (is_null($table))
            $table = '`creature_ai_scripts`';

        if (preg_match('/npc (\d+)/', $kai, $npc))
            $npc = $npc[1];
        else
            $npc = 0;

        // get rid of comments
        $kai = preg_replace('/\s+#.+/', '', $kai);

        // substitute events
        foreach (Kasai::$events as $type => $id)
            $kai = preg_replace("/{$type}/", "event {$id}", $kai);

        // substitute actions
        foreach (Kasai::$actions as $type => $id)
            $kai = preg_replace("/{$type}/", "action {$id}", $kai);

        // substitute flags
        foreach (Kasai::$flags as $flag => $id)
            $kai = preg_replace("/{$flag}/", "flag {$id}", $kai);

        // substitute unit flags
        foreach (Kasai::$unitflags as $flag => $id)
            $kai = preg_replace("/{$flag}/", $id, $kai);

        // substitute other keywords
        foreach (Kasai::$keywords as $k => $v)
            $kai = preg_replace("/{$k}/", $v, $kai);

        // evaluate pipe operator
        $kai = preg_replace_callback('/(\d+)\|(\d+)/', function($m) { return $m[1] | $m[2]; }, $kai);

        // expand cascades
        $tree = array();
        Kasai::expand(&$tree, $kai);
        $result = array();
        Kasai::flatten('', $tree, &$result);

        return "INSERT INTO {$table} VALUES " . PHP_EOL . implode(',' . PHP_EOL, $result) . ';';
    }
}
