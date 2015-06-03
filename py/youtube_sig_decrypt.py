import re
import sys
import json
from urllib import urlopen
from jsinterp import JSInterpreter

def _search_regex(pattern, string, name, fatal=True, flags=0, group=None):
    if isinstance(pattern, str):
        mobj = re.search(pattern, string, flags)
    else:
        for p in pattern:
            mobj = re.search(p, string, flags)
            if mobj:
                break
    if mobj:
        if group is None:
            return next(g for g in mobj.groups() if g is not None)
        else:
            return mobj.group(group)

def _parse_sig_js(jscode):
    funcname = _search_regex(
        r'\.sig\|\|([a-zA-Z0-9$]+)\(', jscode,
        'Initial JS player signature function name')
    jsi = JSInterpreter(jscode)
    initial_function = jsi.extract_function(funcname)
    return lambda s: initial_function([s])

def _extract_signature_function(player_url, sig_list):
    id_m = re.match(
        r'.*?-(?P<id>[a-zA-Z0-9_-]+)(?:/watch_as3|/html5player)?\.(?P<ext>[a-z]+)$',
        player_url)
    player_type = id_m.group('ext')
    player_id = id_m.group('id')
    if player_type == 'js':
        code = urlopen(player_url).read()
        res = _parse_sig_js(code)
    return json.dumps([{"signDecrypted":res(sig), "signEncrypted": sig} for sig in sig_list])

url = sys.argv[1]
sigs = sys.argv[2]

print _extract_signature_function(url, sigs.split(","))
