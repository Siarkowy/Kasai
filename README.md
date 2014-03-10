![Logo](php/kasai.png) Kasai
============================

EventAI SQL generator.

Kasai (from Cascading AI scripts) is a scripting language, which goal is to make
ScriptDev2-like EventAI generation easy. It translates to SQL (as of now by means
of PHP-driven interpreter) and provides the developer human-readable markup with
intuitive keywords.

### Example Kasai script

```lua
npc 15371 Arcatraz Sentinel
{
    timerooc 0.5s {
        normal { cast 36716 self interrupt|triggered }
        heroic { cast 38828 self interrupt|triggered }
    }

    aggro {
        normal { morph 4 setphase 1 }
        heroic { morph 5 setphase 1 }
    }

    repeatable timer 5s 10s 10s 15s { threatallpct -100 }

    # phase 1

    notphase 2 {
        health 12 { setphase 2 setflag nonattackable }
    }

    # phase 2

    notphase 1 {
        repeatable timer 0 0 8s 8s {
            normal { cast 36719 self triggered }
            heroic { cast 38830 self triggered }
        }
    }

    evade { setphase 0 removeflag nonattackable }
}
```

### Output

```sql
INSERT INTO `creature_ai_scripts` VALUES 
(null, 15371, 1, 0, 100, 2, 0.5, 0, 0, 0, 11, 36716, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - timerooc - cast 36716 - normal'),
(null, 15371, 1, 0, 100, 4, 0.5, 0, 0, 0, 11, 38828, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - timerooc - cast 38828 - heroic'),
(null, 15371, 4, 0, 100, 2, 0, 0, 0, 0, 3, 4, 0, 0, 22, 1, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - aggro - morph 4 - setphase 1 - normal'),
(null, 15371, 4, 0, 100, 4, 0, 0, 0, 0, 3, 5, 0, 0, 22, 1, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - aggro - morph 5 - setphase 1 - heroic'),
(null, 15371, 0, 0, 100, 7, 5, 10, 10, 15, 14, -100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - timer - threatallpct -100 - repeatable'),
(null, 15371, 2, 2, 100, 6, 12, 0, 0, 0, 22, 2, 0, 0, 18, 2, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - health - setphase 2 - setflag 2 - notphase 2'),
(null, 15371, 0, 1, 100, 3, 0, 0, 8, 8, 11, 36719, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - timer - cast 36719 - notphase 1 - repeatable - normal'),
(null, 15371, 0, 1, 100, 5, 0, 0, 8, 8, 11, 38830, 0, 2, 0, 0, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - timer - cast 38830 - notphase 1 - repeatable - heroic'),
(null, 15371, 7, 0, 100, 6, 0, 0, 0, 0, 22, 0, 0, 0, 19, 2, 0, 0, 0, 0, 0, 0, 'Arcatraz Sentinel - evade - setphase 0 - removeflag 2');
```
