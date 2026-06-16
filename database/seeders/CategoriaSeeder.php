<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    private function upsert(string $nome, array $dados = []): Categoria
    {
        $ativo = Categoria::where('nome', $nome)->first();
        if ($ativo) {
            return $ativo;
        }

        $deletado = Categoria::withTrashed()->where('nome', $nome)->first();
        if ($deletado) {
            $deletado->restore();
            return $deletado;
        }

        return Categoria::create(array_merge(['nome' => $nome], $dados));
    }

    private function traducoes(array $index, string $nome): array
    {
        $tuid = strtoupper($nome);
        if (!isset($index[$tuid])) {
            return [];
        }

        $map = ['EN-US' => 'ingles', 'FR-FR' => 'frances', 'ES-ES' => 'espanhol', 'PT-BR' => 'portugues'];
        $result = [];
        foreach ($index[$tuid] as $lang => $seg) {
            if (isset($map[$lang])) {
                $result[$map[$lang]] = $seg;
            }
        }
        return $result;
    }

    public function run(): void
    {
        $jsonPath = base_path('categorias.json');
        $jsonIndex = [];
        if (file_exists($jsonPath)) {
            $entries = json_decode(file_get_contents($jsonPath), true);
            foreach ($entries as $entry) {
                $tuid = $entry['@tuid'];
                $langs = [];
                $tuvs = isset($entry['tuv']['@xml:lang']) ? [$entry['tuv']] : $entry['tuv'];
                foreach ($tuvs as $tuv) {
                    $langs[$tuv['@xml:lang']] = $tuv['seg'];
                }
                $jsonIndex[$tuid] = $langs;
            }
        }

        $xml = simplexml_load_string('
        <shopcategories>
            <shopcategory id="head" value="3000000">
            <shopsubcategory id="cloth_helmet" value="10000">
                <shopsubcategory2 id="head_cloth_set1" value="100" />
                <shopsubcategory2 id="head_cloth_set2" value="200" />
                <shopsubcategory2 id="head_cloth_set3" value="300" />
                <shopsubcategory2 id="head_cloth_keeper" value="400" />
                <shopsubcategory2 id="head_cloth_royal" value="500" />
                <shopsubcategory2 id="head_cloth_hell" value="600" />
                <shopsubcategory2 id="head_cloth_morgana" value="700" />
                <shopsubcategory2 id="head_cloth_fey" value="800" />
                <shopsubcategory2 id="head_cloth_avalon" value="900" />
            </shopsubcategory>
            <shopsubcategory id="leather_helmet" value="20000">
                <shopsubcategory2 id="head_leather_set1" value="100" />
                <shopsubcategory2 id="head_leather_set2" value="200" />
                <shopsubcategory2 id="head_leather_set3" value="300" />
                <shopsubcategory2 id="head_leather_morgana" value="400" />
                <shopsubcategory2 id="head_leather_royal" value="500" />
                <shopsubcategory2 id="head_leather_hell" value="600" />
                <shopsubcategory2 id="head_leather_undead" value="700" />
                <shopsubcategory2 id="head_leather_fey" value="800" />
                <shopsubcategory2 id="head_leather_avalon" value="900" />
            </shopsubcategory>
            <shopsubcategory id="plate_helmet" value="30000">
                <shopsubcategory2 id="head_plate_set1" value="100" />
                <shopsubcategory2 id="head_plate_set2" value="200" />
                <shopsubcategory2 id="head_plate_set3" value="300" />
                <shopsubcategory2 id="head_plate_undead" value="400" />
                <shopsubcategory2 id="head_plate_royal" value="500" />
                <shopsubcategory2 id="head_plate_hell" value="600" />
                <shopsubcategory2 id="head_plate_keeper" value="700" />
                <shopsubcategory2 id="head_plate_fey" value="800" />
                <shopsubcategory2 id="head_plate_avalon" value="900" />
            </shopsubcategory>
            <shopsubcategory id="other" value="40000" hideindropdown="true" />
            </shopcategory>
            <shopcategory id="shoes" value="4000000">
            <shopsubcategory id="cloth_shoes" value="10000">
                <shopsubcategory2 id="shoes_cloth_set1" value="100" />
                <shopsubcategory2 id="shoes_cloth_set2" value="200" />
                <shopsubcategory2 id="shoes_cloth_set3" value="300" />
                <shopsubcategory2 id="shoes_cloth_keeper" value="400" />
                <shopsubcategory2 id="shoes_cloth_royal" value="500" />
                <shopsubcategory2 id="shoes_cloth_hell" value="600" />
                <shopsubcategory2 id="shoes_cloth_morgana" value="700" />
                <shopsubcategory2 id="shoes_cloth_fey" value="800" />
                <shopsubcategory2 id="shoes_cloth_avalon" value="900" />
            </shopsubcategory>
            <shopsubcategory id="leather_shoes" value="20000">
                <shopsubcategory2 id="shoes_leather_set1" value="100" />
                <shopsubcategory2 id="shoes_leather_set2" value="200" />
                <shopsubcategory2 id="shoes_leather_set3" value="300" />
                <shopsubcategory2 id="shoes_leather_morgana" value="400" />
                <shopsubcategory2 id="shoes_leather_royal" value="500" />
                <shopsubcategory2 id="shoes_leather_hell" value="600" />
                <shopsubcategory2 id="shoes_leather_undead" value="700" />
                <shopsubcategory2 id="shoes_leather_fey" value="800" />
                <shopsubcategory2 id="shoes_leather_avalon" value="900" />
            </shopsubcategory>
            <shopsubcategory id="plate_shoes" value="30000">
                <shopsubcategory2 id="shoes_plate_set1" value="100" />
                <shopsubcategory2 id="shoes_plate_set2" value="200" />
                <shopsubcategory2 id="shoes_plate_set3" value="300" />
                <shopsubcategory2 id="shoes_plate_undead" value="400" />
                <shopsubcategory2 id="shoes_plate_royal" value="500" />
                <shopsubcategory2 id="shoes_plate_hell" value="600" />
                <shopsubcategory2 id="shoes_plate_keeper" value="700" />
                <shopsubcategory2 id="shoes_plate_fey" value="800" />
                <shopsubcategory2 id="shoes_plate_avalon" value="900" />
            </shopsubcategory>
            <shopsubcategory id="other" value="40000" />
            </shopcategory>
            <shopcategory id="offhands" value="5000000">
            <shopsubcategory id="booktype" value="10000">
                <shopsubcategory2 id="booktype_book" value="100" />
                <shopsubcategory2 id="booktype_morgana" value="200" />
                <shopsubcategory2 id="booktype_hellsskull" value="300" />
                <shopsubcategory2 id="booktype_keeper" value="400" />
                <shopsubcategory2 id="booktype_avalon" value="500" />
                <shopsubcategory2 id="booktype_crystal" value="600" />
            </shopsubcategory>
            <shopsubcategory id="torchtype" value="20000">
                <shopsubcategory2 id="torchtype_torch" value="100" />
                <shopsubcategory2 id="torchtype_keeperhorn" value="200" />
                <shopsubcategory2 id="torchtype_hell" value="300" />
                <shopsubcategory2 id="torchtype_undead" value="400" />
                <shopsubcategory2 id="torchtype_avalon" value="500" />
                <shopsubcategory2 id="torchtype_crystal" value="600" />
            </shopsubcategory>
            <shopsubcategory id="shieldtype" value="30000">
                <shopsubcategory2 id="shieldtype_shield" value="100" />
                <shopsubcategory2 id="shieldtype_undead" value="200" />
                <shopsubcategory2 id="shieldtype_hell" value="300" />
                <shopsubcategory2 id="shieldtype_morgana" value="400" />
                <shopsubcategory2 id="shieldtype_avalon" value="500" />
                <shopsubcategory2 id="shieldtype_crystal" value="600" />
            </shopsubcategory>
            <shopsubcategory id="other" value="40000" hideindropdown="true" />
            </shopcategory>
            <shopcategory id="capes" value="6000000">
            <shopsubcategory id="accessoires_capes_capes" value="10000" />
            <shopsubcategory id="accessoires_capes_bridgewatch" value="20000" />
            <shopsubcategory id="accessoires_capes_fortsterling" value="30000" />
            <shopsubcategory id="accessoires_capes_lymhurst" value="40000" />
            <shopsubcategory id="accessoires_capes_martlock" value="50000" />
            <shopsubcategory id="accessoires_capes_thetford" value="60000" />
            <shopsubcategory id="accessoires_capes_caerleon" value="70000" />
            <shopsubcategory id="accessoires_capes_brecilien" value="80000" />
            <shopsubcategory id="accessoires_capes_heretic" value="90000" />
            <shopsubcategory id="accessoires_capes_undead" value="100000" />
            <shopsubcategory id="accessoires_capes_keeper" value="110000" />
            <shopsubcategory id="accessoires_capes_morgana" value="120000" />
            <shopsubcategory id="accessoires_capes_avalon" value="130000" />
            <shopsubcategory id="accessoires_capes_demon" value="140000" />
            <shopsubcategory id="accessoires_capes_smuggler" value="150000" />
            <shopsubcategory id="other" value="160000" hideindropdown="true" />
            </shopcategory>
            <shopcategory id="bags" value="7000000">
            <shopsubcategory id="bags" value="10000" />
            <shopsubcategory id="satchels" value="20000" />
            <shopsubcategory id="other" value="30000" hideindropdown="true" />
            </shopcategory>
            <shopcategory id="mounts" value="8000000">
            <shopsubcategory id="basemounts" value="10000">
                <shopsubcategory2 id="horse" value="100" />
                <shopsubcategory2 id="armoredhorse" value="200" />
                <shopsubcategory2 id="ox" value="300" />
                <shopsubcategory2 id="ram" value="400" />
                <shopsubcategory2 id="direwolf" value="500" />
                <shopsubcategory2 id="stag" value="600" />
                <shopsubcategory2 id="cougar" value="700" />
                <shopsubcategory2 id="direboar" value="800" />
                <shopsubcategory2 id="direbear" value="900" />
                <shopsubcategory2 id="swampdragon" value="1000" />
                <shopsubcategory2 id="donkey" value="1100" />
                <shopsubcategory2 id="mammoth" value="1200" />
                <shopsubcategory2 id="moabird" value="1300" />
            </shopsubcategory>
            <shopsubcategory id="raremounts" value="20000">
                <shopsubcategory2 id="horse" value="100" />
                <shopsubcategory2 id="armoredhorse" value="200" />
                <shopsubcategory2 id="ram" value="300" />
                <shopsubcategory2 id="direwolf" value="400" />
                <shopsubcategory2 id="stag" value="600" hideindropdown="true" />
                <shopsubcategory2 id="cougar" value="700" />
                <shopsubcategory2 id="direboar" value="800" />
                <shopsubcategory2 id="direbear" value="900" />
                <shopsubcategory2 id="swampdragon" value="1000" />
                <shopsubcategory2 id="donkey" value="1100" />
                <shopsubcategory2 id="bat" value="1200" />
                <shopsubcategory2 id="moabird" value="1300" />
                <shopsubcategory2 id="owl" value="1400" />
                <shopsubcategory2 id="rabbit" value="1500" />
                <shopsubcategory2 id="raven" value="1600" />
                <shopsubcategory2 id="spider" value="1700" />
                <shopsubcategory2 id="toad" value="1800" />
            </shopsubcategory>
            <shopsubcategory id="battle_mount" value="30000">
                <shopsubcategory2 id="bastion" value="100" />
                <shopsubcategory2 id="beetle" value="200" />
                <shopsubcategory2 id="behemenoth" value="300" />
                <shopsubcategory2 id="chariot" value="400" />
                <shopsubcategory2 id="eagle" value="500" />
                <shopsubcategory2 id="ent" value="600" />
                <shopsubcategory2 id="juggernaut" value="700" />
                <shopsubcategory2 id="mammoth" value="800" />
                <shopsubcategory2 id="rhino" value="900" />
                <shopsubcategory2 id="siegeballista" value="1000" />
                <shopsubcategory2 id="spider" value="1100" />
                <shopsubcategory2 id="swampdragon" value="1200" />
                <shopsubcategory2 id="tankbeetle" value="1300" />
            </shopsubcategory>
            </shopcategory>
            <shopcategory id="consumables" value="9000000">
            <shopsubcategory id="food" value="10000">
                <shopsubcategory2 id="soups" value="200" />
                <shopsubcategory2 id="salads" value="300" />
                <shopsubcategory2 id="pies" value="400" />
                <shopsubcategory2 id="roasts" value="500" />
                <shopsubcategory2 id="omelettes" value="600" />
                <shopsubcategory2 id="stews" value="700" />
                <shopsubcategory2 id="sandwiches" value="800" />
                <shopsubcategory2 id="grilledfish" value="900" />
                <shopsubcategory2 id="event" value="1000" />
            </shopsubcategory>
            <shopsubcategory id="potions" value="20000">
                <shopsubcategory2 id="heal" value="200" />
                <shopsubcategory2 id="energy" value="300" />
                <shopsubcategory2 id="gigantify" value="400" />
                <shopsubcategory2 id="resistance" value="500" />
                <shopsubcategory2 id="slowfield" value="600" />
                <shopsubcategory2 id="poison" value="700" />
                <shopsubcategory2 id="invisibility" value="800" />
                <shopsubcategory2 id="calming" value="900" />
                <shopsubcategory2 id="cleanse" value="1000" />
                <shopsubcategory2 id="acid" value="1100" />
                <shopsubcategory2 id="berserk" value="1200" />
                <shopsubcategory2 id="lava" value="1300" />
                <shopsubcategory2 id="gather" value="1400" />
                <shopsubcategory2 id="tornado" value="1400" />
                <shopsubcategory2 id="focus" value="1500" />
                <shopsubcategory2 id="event" value="1600" />
                <shopsubcategory2 id="other" value="2000" hideindropdown="true" />
            </shopsubcategory>
            <shopsubcategory id="tomes" value="30000">
                <shopsubcategory2 id="fame" value="100" />
                <shopsubcategory2 id="fiber" value="200" />
                <shopsubcategory2 id="hide" value="300" />
                <shopsubcategory2 id="ore" value="400" />
                <shopsubcategory2 id="rock" value="500" />
                <shopsubcategory2 id="wood" value="600" />
                <shopsubcategory2 id="learningpoints" value="300" hideindropdown="true" />
            </shopsubcategory>
            <shopsubcategory id="other" value="40000">
                <shopsubcategory2 id="repairpowder" value="100" />
                <shopsubcategory2 id="firework" value="200" />
                <shopsubcategory2 id="other" value="300" hideindropdown="true" />
            </shopsubcategory>
            <shopsubcategory id="silverbag" value="70000" hideindropdown="true" />
            </shopcategory>
            <shopcategory id="gathering" value="10000000">
            <shopsubcategory id="fish" value="10000">
                <shopsubcategory2 id="fishingrods" value="100" />
                <shopsubcategory2 id="gathering_armor" value="200" />
                <shopsubcategory2 id="gathering_head" value="300" />
                <shopsubcategory2 id="gathering_shoes" value="400" />
                <shopsubcategory2 id="gathering_capes" value="500" />
                <shopsubcategory2 id="bait" value="600" />
            </shopsubcategory>
            <shopsubcategory id="fiber" value="20000">
                <shopsubcategory2 id="sickle" value="100" />
                <shopsubcategory2 id="gathering_armor" value="200" />
                <shopsubcategory2 id="gathering_head" value="300" />
                <shopsubcategory2 id="gathering_shoes" value="400" />
                <shopsubcategory2 id="gathering_capes" value="500" />
            </shopsubcategory>
            <shopsubcategory id="hide" value="30000">
                <shopsubcategory2 id="knifes" value="100" />
                <shopsubcategory2 id="gathering_armor" value="200" />
                <shopsubcategory2 id="gathering_head" value="300" />
                <shopsubcategory2 id="gathering_shoes" value="400" />
                <shopsubcategory2 id="gathering_capes" value="500" />
            </shopsubcategory>
            <shopsubcategory id="ore" value="40000">
                <shopsubcategory2 id="picks" value="100" />
                <shopsubcategory2 id="gathering_armor" value="200" />
                <shopsubcategory2 id="gathering_head" value="300" />
                <shopsubcategory2 id="gathering_shoes" value="400" />
                <shopsubcategory2 id="gathering_capes" value="500" />
            </shopsubcategory>
            <shopsubcategory id="rock" value="50000">
                <shopsubcategory2 id="hammer" value="100" />
                <shopsubcategory2 id="gathering_armor" value="200" />
                <shopsubcategory2 id="gathering_head" value="300" />
                <shopsubcategory2 id="gathering_shoes" value="400" />
                <shopsubcategory2 id="gathering_capes" value="500" />
            </shopsubcategory>
            <shopsubcategory id="wood" value="60000">
                <shopsubcategory2 id="axes" value="100" />
                <shopsubcategory2 id="gathering_armor" value="200" />
                <shopsubcategory2 id="gathering_head" value="300" />
                <shopsubcategory2 id="gathering_shoes" value="400" />
                <shopsubcategory2 id="gathering_capes" value="500" />
            </shopsubcategory>
            <shopsubcategory id="tracking" value="70000">
                <shopsubcategory2 id="toolkit" value="100" />
            </shopsubcategory>
            </shopcategory>
            <shopcategory id="crafting" value="11000000">
            <shopsubcategory id="resources" value="10000">
                <shopsubcategory2 id="fiber" value="100" />
                <shopsubcategory2 id="hide" value="200" />
                <shopsubcategory2 id="ore" value="300" />
                <shopsubcategory2 id="rock" value="400" />
                <shopsubcategory2 id="wood" value="500" />
            </shopsubcategory>
            <shopsubcategory id="refinedresources" value="20000">
                <shopsubcategory2 id="cloth" value="100" />
                <shopsubcategory2 id="leather" value="200" />
                <shopsubcategory2 id="metalbars" value="300" />
                <shopsubcategory2 id="stoneblock" value="400" />
                <shopsubcategory2 id="planks" value="500" />
            </shopsubcategory>
            <shopsubcategory id="tokens" value="90000">
                <shopsubcategory2 id="mountupgrades" value="200" />
                <shopsubcategory2 id="royalsigils" value="300" />
                <shopsubcategory2 id="other" value="500" />
            </shopsubcategory>
            <shopsubcategory id="cityresources" value="100000">
                <shopsubcategory2 id="beastheart" value="100" />
                <shopsubcategory2 id="treeheart" value="200" />
                <shopsubcategory2 id="mountainheart" value="300" />
                <shopsubcategory2 id="rockheart" value="400" />
                <shopsubcategory2 id="vineheart" value="500" />
                <shopsubcategory2 id="blackheart" value="600" />
            </shopsubcategory>
            <shopsubcategory id="fish" value="120000">
                <shopsubcategory2 id="common" value="100" />
                <shopsubcategory2 id="rare" value="200" />
                <shopsubcategory2 id="other" value="300" />
            </shopsubcategory>
            <shopsubcategory id="alchemy" value="130000">
                <shopsubcategory2 id="remains_panther" value="100" />
                <shopsubcategory2 id="remains_ent" value="200" />
                <shopsubcategory2 id="remains_direbear" value="300" />
                <shopsubcategory2 id="remains_werewolf" value="400" />
                <shopsubcategory2 id="remains_imp" value="500" />
                <shopsubcategory2 id="remains_elemental" value="600" />
                <shopsubcategory2 id="remains_eagle" value="700" />
                <shopsubcategory2 id="remains" value="800" />
                <shopsubcategory2 id="extract" value="900" />
                <shopsubcategory2 id="essence" value="1000" />
            </shopsubcategory>
            </shopcategory>
            <shopcategory id="artefacts" value="12000000">
            <shopsubcategory id="weapons" value="30000">
                <shopsubcategory2 id="bow" value="100" />
                <shopsubcategory2 id="crossbow" value="200" />
                <shopsubcategory2 id="axe" value="300" />
                <shopsubcategory2 id="dagger" value="400" />
                <shopsubcategory2 id="hammer" value="500" />
                <shopsubcategory2 id="knuckles" value="600" />
                <shopsubcategory2 id="mace" value="700" />
                <shopsubcategory2 id="quarterstaff" value="800" />
                <shopsubcategory2 id="spear" value="900" />
                <shopsubcategory2 id="sword" value="1000" />
                <shopsubcategory2 id="arcanestaff" value="1100" />
                <shopsubcategory2 id="cursestaff" value="1200" />
                <shopsubcategory2 id="firestaff" value="1300" />
                <shopsubcategory2 id="froststaff" value="1400" />
                <shopsubcategory2 id="holystaff" value="1500" />
                <shopsubcategory2 id="naturestaff" value="1600" />
                <shopsubcategory2 id="shapeshifterstaff" value="1700" />
            </shopsubcategory>
            <shopsubcategory id="armors" value="40000">
                <shopsubcategory2 id="cloth_armor" value="100" />
                <shopsubcategory2 id="leather_armor" value="200" />
                <shopsubcategory2 id="plate_armor" value="300" />
            </shopsubcategory>
            <shopsubcategory id="head" value="50000">
                <shopsubcategory2 id="cloth_helmet" value="100" />
                <shopsubcategory2 id="leather_helmet" value="200" />
                <shopsubcategory2 id="plate_helmet" value="300" />
            </shopsubcategory>
            <shopsubcategory id="shoes" value="60000">
                <shopsubcategory2 id="cloth_shoes" value="100" />
                <shopsubcategory2 id="leather_shoes" value="200" />
                <shopsubcategory2 id="plate_shoes" value="300" />
            </shopsubcategory>
            <shopsubcategory id="offhands" value="70000">
                <shopsubcategory2 id="booktype" value="100" />
                <shopsubcategory2 id="torchtype" value="200" />
                <shopsubcategory2 id="shieldtype" value="300" />
            </shopsubcategory>
            <shopsubcategory id="fragments" value="80000">
                <shopsubcategory2 id="runes" value="100" />
                <shopsubcategory2 id="souls" value="200" />
                <shopsubcategory2 id="relics" value="300" />
                <shopsubcategory2 id="avalonianshards" value="400" />
                <shopsubcategory2 id="crystalshards" value="500" />
            </shopsubcategory>
            <shopsubcategory id="capes" value="90000">
                <shopsubcategory2 id="tokens_capes_bridgewatch" value="100" />
                <shopsubcategory2 id="tokens_capes_fortsterling" value="200" />
                <shopsubcategory2 id="tokens_capes_lymhurst" value="300" />
                <shopsubcategory2 id="tokens_capes_martlock" value="400" />
                <shopsubcategory2 id="tokens_capes_thetford" value="500" />
                <shopsubcategory2 id="tokens_capes_caerleon" value="600" />
                <shopsubcategory2 id="tokens_capes_brecilien" value="700" />
                <shopsubcategory2 id="tokens_capes_heretics" value="800" />
                <shopsubcategory2 id="tokens_capes_undead" value="900" />
                <shopsubcategory2 id="tokens_capes_keeper" value="1000" />
                <shopsubcategory2 id="tokens_capes_morgana" value="1100" />
                <shopsubcategory2 id="tokens_capes_demon" value="1200" />
                <shopsubcategory2 id="tokens_capes_avalon" value="1300" />
                <shopsubcategory2 id="tokens_capes_smuggler" value="1400" />
            </shopsubcategory>
            <shopsubcategory id="favor" value="100000" />
            </shopcategory>
            <shopcategory id="farming" value="13000000">
            <shopsubcategory id="farm" value="10000">
                <shopsubcategory2 id="seeds" value="100" />
                <shopsubcategory2 id="plants" value="200" />
            </shopsubcategory>
            <shopsubcategory id="herbgarden" value="20000">
                <shopsubcategory2 id="farming-seeds" value="100" />
                <shopsubcategory2 id="herbs" value="200" />
            </shopsubcategory>
            <shopsubcategory id="pasture" value="30000">
                <shopsubcategory2 id="babys" value="100" />
                <shopsubcategory2 id="animals" value="200" />
                <shopsubcategory2 id="egg" value="300" />
                <shopsubcategory2 id="milk" value="400" />
            </shopsubcategory>
            <shopsubcategory id="kennel" value="40000">
                <shopsubcategory2 id="farming-babys" value="100" />
                <shopsubcategory2 id="farming-animals" value="200" />
            </shopsubcategory>
            <shopsubcategory id="farmingproducts" value="50000">
                <shopsubcategory2 id="alcohol" value="100" />
                <shopsubcategory2 id="bread" value="200" />
                <shopsubcategory2 id="butter" value="300" />
                <shopsubcategory2 id="flour" value="400" />
                <shopsubcategory2 id="meat" value="500" />
            </shopsubcategory>
            </shopcategory>
            <shopcategory id="furniture" value="14000000">
            <shopsubcategory id="repairkit" value="10000" />
            <shopsubcategory id="chest" value="20000">
                <shopsubcategory2 id="house" value="100" />
                <shopsubcategory2 id="furniture-world" value="200" />
            </shopsubcategory>
            <shopsubcategory id="house" value="30000" />
            <shopsubcategory id="island" value="40000" />
            <shopsubcategory id="world" value="50000">
                <shopsubcategory2 id="banner" value="100" />
                <shopsubcategory2 id="statue" value="200" />
                <shopsubcategory2 id="furniture-other" value="300" />
            </shopsubcategory>
            <shopsubcategory id="furniture-other" value="60000" hideindropdown="true" />
            </shopcategory>
            <shopcategory id="vanity" value="15000000">
            <shopsubcategory id="avatar" value="10000" hideindropdown="true">
                <shopsubcategory2 id="journal" value="100" />
                <shopsubcategory2 id="adventurerchallenge" value="200" />
                <shopsubcategory2 id="gvgseason" value="300" />
                <shopsubcategory2 id="anniversary" value="400" />
                <shopsubcategory2 id="vanity-other" value="500" />
            </shopsubcategory>
            <shopsubcategory id="avatar" value="20000" hideindropdown="true">
                <shopsubcategory2 id="journal" value="100" />
                <shopsubcategory2 id="adventurerchallenge" value="200" />
                <shopsubcategory2 id="gvgseason" value="300" />
                <shopsubcategory2 id="anniversary" value="400" />
                <shopsubcategory2 id="other" value="500" />
            </shopsubcategory>
            <shopsubcategory id="vanity-mounts" value="30000">
                <shopsubcategory2 id="vanity-horse" value="100" />
                <shopsubcategory2 id="vanity-armoredhorse" value="200" />
                <shopsubcategory2 id="vanity-ox" value="300" />
                <shopsubcategory2 id="vanity-direwolf" value="400" />
                <shopsubcategory2 id="vanity-giantstag" value="500" />
                <shopsubcategory2 id="vanity-cougar" value="600" />
                <shopsubcategory2 id="vanity-direboar" value="700" />
                <shopsubcategory2 id="vanity-direbear" value="800" />
                <shopsubcategory2 id="vanity-lizard" value="900" />
                <shopsubcategory2 id="vanity-donkey" value="1000" />
                <shopsubcategory2 id="vanity-mammoth" value="1100" />
            </shopsubcategory>
            <shopsubcategory id="vanity-weapons" value="40000" />
            <shopsubcategory id="vanity-armors" value="50000" />
            <shopsubcategory id="vanity-head" value="60000" />
            <shopsubcategory id="vanity-shoes" value="70000" />
            <shopsubcategory id="vanity-offhands" value="80000" />
            <shopsubcategory id="vanity-capes" value="90000" />
            <shopsubcategory id="vanity-killemotes" value="100000" />
            </shopcategory>
            <shopcategory id="other" value="16000000">
            <shopsubcategory id="lootitem" value="10000" hideindropdown="true" />
            <shopsubcategory id="guilds" value="20000">
                <shopsubcategory2 id="hideout" value="100" />
                <shopsubcategory2 id="siegehammer" value="200" />
                <shopsubcategory2 id="siegebanners" value="300" />
            </shopsubcategory>
            <shopsubcategory id="labourers" value="30000">
                <shopsubcategory2 id="journals" value="100" />
                <shopsubcategory2 id="trophies" value="200" />
                <shopsubcategory2 id="contracts" value="300" />
                <shopsubcategory2 id="beds" value="400" />
                <shopsubcategory2 id="tables" value="500" />
            </shopsubcategory>
            <shopsubcategory id="tokens" value="40000">
                <shopsubcategory2 id="vanity" value="100" />
                <shopsubcategory2 id="crystalleague" value="200" />
                <shopsubcategory2 id="anchors" value="300" />
                <shopsubcategory2 id="other-other" value="400" />
            </shopsubcategory>
            <shopsubcategory id="luxurygoods" value="50000">
                <shopsubcategory2 id="bridgewatch" value="100" />
                <shopsubcategory2 id="fortsterling" value="200" />
                <shopsubcategory2 id="lymhurst" value="300" />
                <shopsubcategory2 id="martlock" value="400" />
                <shopsubcategory2 id="thetford" value="500" />
                <shopsubcategory2 id="caerleon" value="600" />
                <shopsubcategory2 id="any" value="700" />
            </shopsubcategory>
            <shopsubcategory id="maps" value="60000">
                <shopsubcategory2 id="randomdungeons" value="100" />
                <shopsubcategory2 id="corrupteddungeons" value="200" />
                <shopsubcategory2 id="hellgates" value="300" />
                <shopsubcategory2 id="other-other" value="400" hideindropdown="true" />
            </shopsubcategory>
            <shopsubcategory id="hardcoreexpeditions" value="70000">
                <shopsubcategory2 id="event" value="100" />
                <shopsubcategory2 id="fishybusiness" value="200" />
                <shopsubcategory2 id="fistfulofsilver" value="300" />
                <shopsubcategory2 id="lumbercamp" value="400" />
                <shopsubcategory2 id="mushroom" value="500" />
                <shopsubcategory2 id="recruitment" value="600" />
                <shopsubcategory2 id="sewerscrypt" value="700" />
                <shopsubcategory2 id="stonewars" value="800" />
                <shopsubcategory2 id="threesisters" value="900" />
                <shopsubcategory2 id="torturer" value="1000" />
                <shopsubcategory2 id="eternalbattle" value="1100" />
            </shopsubcategory>
            <shopsubcategory id="questitems" value="80000">
                <shopsubcategory2 id="cities" value="100" />
                <shopsubcategory2 id="other-other" value="200" />
            </shopsubcategory>
            <shopsubcategory id="killtrophy" value="100000" hideindropdown="true" />
            <shopsubcategory id="trash" value="110000" hideindropdown="true" />
            <shopsubcategory id="other-other" value="120000" hideindropdown="true" />
            </shopcategory>
        </shopcategories>
        ');

        
        foreach ($xml->shopcategory as $avo) {
            $nomeAvo = (string) $avo['id'];
            $categoriaAvo = $this->upsert($nomeAvo, $this->traducoes($jsonIndex, $nomeAvo));

            foreach ($avo->shopsubcategory as $filho) {
                $nomeFilho = (string) $filho['id'];
                $categoriaFilho = $this->upsert(
                    $nomeFilho,
                    ['categoria_pai_id' => $categoriaAvo->id] + $this->traducoes($jsonIndex, $nomeFilho)
                );

                foreach ($filho->shopsubcategory2 as $neto) {
                    $nomeNeto = (string) $neto['id'];
                    $this->upsert(
                        $nomeNeto,
                        ['categoria_pai_id' => $categoriaFilho->id] + $this->traducoes($jsonIndex, $nomeNeto)
                    );
                }
            }
        }
    }
}
