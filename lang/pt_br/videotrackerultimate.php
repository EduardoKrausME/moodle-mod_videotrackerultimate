<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings em Português do Brasil.
 *
 * @package mod_videotrackerultimate
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['actual'] = 'Valor observado';
$string['addindicator'] = 'Adicionar indicador';
$string['adequatelabel'] = 'Rótulo da segunda categoria';
$string['adequatemin'] = 'Mínimo da segunda categoria';
$string['attentionlabel'] = 'Rótulo da terceira categoria';
$string['attentionmin'] = 'Mínimo da terceira categoria';
$string['averagepercent'] = 'Percentual médio assistido';
$string['averagescore'] = 'Score médio';
$string['averagewatchtime'] = 'Tempo efetivo médio';
$string['backtoactivity'] = 'Voltar para a atividade';
$string['backtooverview'] = 'Voltar para a visão geral';
$string['belowthreshold'] = 'Alunos abaixo do limite de revisão';
$string['calculationorigin'] = 'Origem do cálculo: {$a}';
$string['classoverview'] = 'Visão geral da turma';
$string['completiondetail:indicators'] = 'Cumprir todos os indicadores marcados como obrigatórios para conclusão';
$string['completiondetail:percent'] = 'Assistir pelo menos {$a}% do vídeo';
$string['completiondetail:score'] = 'Obter Engagement Score de pelo menos {$a}';
$string['completionindicators'] = 'Exigir indicadores marcados como obrigatórios para conclusão';
$string['completionminpercent'] = 'Exigir percentual mínimo assistido';
$string['completionminscore'] = 'Exigir Engagement Score mínimo';
$string['editindicator'] = 'Editar indicador';
$string['effectivetime'] = 'Tempo efetivo de reprodução';
$string['engagementscore'] = 'Engagement Score';
$string['errorgrademax'] = 'A nota máxima deve ser maior que zero e não pode ultrapassar 10000.';
$string['errorpercent'] = 'O valor deve ficar entre 0 e 100.';
$string['errorstatusorder'] = 'Os limites das categorias precisam estar ordenados do maior para o menor.';
$string['errortotalweight'] = 'Os indicadores ativos já usam {$a} pontos. O total ativo não pode ultrapassar 100.';
$string['errorweight'] = 'O peso deve ser maior que zero e não pode ultrapassar 100.';
$string['eventscoreupdated'] = 'Engagement Score atualizado';
$string['evidence'] = 'Evidência';
$string['excellentlabel'] = 'Rótulo da categoria superior';
$string['excellentmin'] = 'Mínimo da categoria superior';
$string['exportcsv'] = 'Exportar CSV';
$string['frequentfailures'] = 'Indicadores não cumpridos com maior frequência';
$string['gradefeedbackorigin'] = 'Origem do recálculo do Video Tracker Ultimate: {$a}';
$string['grademax'] = 'Nota máxima';
$string['indicator'] = 'Indicador';
$string['indicatordeleted'] = 'Indicador excluído. Os scores existentes foram enfileirados para recálculo.';
$string['indicatorenabled'] = 'Ativo';
$string['indicatorname'] = 'Nome';
$string['indicators'] = 'Indicadores';
$string['indicatorsaved'] = 'Indicador salvo. Os scores existentes foram enfileirados para recálculo.';
$string['indicatorweight'] = 'Peso';
$string['individualreport'] = 'Evidências de reprodução de {$a}';
$string['insufficientlabel'] = 'Rótulo da categoria inferior';
$string['lastupdated'] = 'Última atualização dos analytics: {$a}';
$string['lastupdatedlabel'] = 'Última atualização';
$string['learners'] = 'Alunos';
$string['maxposition'] = 'Maior posição alcançada';
$string['maxrate'] = 'Velocidade máxima';
$string['modulename'] = 'Video Tracker Ultimate';
$string['modulename_help'] = 'Cria um Engagement Score explicável a partir de evidências de reprodução explicitamente configuradas.';
$string['modulenameplural'] = 'Atividades Video Tracker Ultimate';
$string['noduration'] = 'A duração do vídeo ainda não é conhecida.';
$string['nofailures'] = 'Não há indicadores pendentes nos resultados atualmente em cache.';
$string['normalizedscoreexplain'] = 'Os indicadores configurados concederam {$a->raw}/{$a->total} pontos brutos, normalizados de forma explícita para {$a->score}/100.';
$string['notcalculated'] = 'Não calculado';
$string['percentwatched'] = 'Percentual assistido';
$string['pluginadministration'] = 'Administração do Video Tracker Ultimate';
$string['pluginname'] = 'Video Tracker Ultimate';
$string['points'] = 'Pontos';
$string['privacy:metadata:log'] = 'Armazena a trilha de auditoria dos recálculos de score.';
$string['privacy:metadata:log:newscore'] = 'Novo score normalizado.';
$string['privacy:metadata:log:oldscore'] = 'Score normalizado anterior.';
$string['privacy:metadata:log:origin'] = 'Motivo do recálculo.';
$string['privacy:metadata:log:timecreated'] = 'Quando o recálculo ocorreu.';
$string['privacy:metadata:log:triggeredby'] = 'Usuário que disparou manualmente o recálculo, quando aplicável.';
$string['privacy:metadata:log:userid'] = 'Aluno cujo score foi recalculado.';
$string['privacy:metadata:score'] = 'Armazena o score determinístico em cache e evidências normalizadas de reprodução.';
$string['privacy:metadata:score:breakdownjson'] = 'Composição completa e explicável dos indicadores.';
$string['privacy:metadata:score:calculatedby'] = 'Usuário que disparou explicitamente o cálculo, quando aplicável.';
$string['privacy:metadata:score:gradeorigin'] = 'Origem do cálculo responsável pela última atualização do Gradebook.';
$string['privacy:metadata:score:gradeupdated'] = 'Quando a nota do Gradebook foi atualizada pela última vez.';
$string['privacy:metadata:score:metricsjson'] = 'Métricas normalizadas obtidas pelas APIs públicas do Video Bridge.';
$string['privacy:metadata:score:origin'] = 'Motivo no servidor que disparou o cálculo.';
$string['privacy:metadata:score:rawscore'] = 'Pontos brutos concedidos antes da normalização explícita.';
$string['privacy:metadata:score:score'] = 'Engagement Score normalizado de 0 a 100.';
$string['privacy:metadata:score:statuscode'] = 'Código da categoria de comportamento de reprodução derivada dos limites configurados.';
$string['privacy:metadata:score:timecalculated'] = 'Quando as evidências em cache foram calculadas.';
$string['privacy:metadata:score:totalweight'] = 'Soma dos pesos ativos usada no cálculo.';
$string['privacy:metadata:score:userid'] = 'O aluno ao qual as evidências pertencem.';
$string['privacy:path'] = 'Evidências do Video Tracker Ultimate';
$string['rank'] = 'Posição';
$string['rankingenabled'] = 'Habilitar ranking somente para professor';
$string['rankingenabled_help'] = 'Quando habilitado, usuários com a capability específica podem ordenar alunos pelo score. Nenhum ranking é exibido aos alunos.';
$string['recalculateanalytics'] = 'Recalcular analytics';
$string['recalculationqueued'] = '{$a} tarefa(s) de recálculo no servidor enfileirada(s).';
$string['replays'] = 'Revisões/replays';
$string['reports'] = 'Relatórios';
$string['requiredcompletion'] = 'Obrigatório para conclusão';
$string['reviewthreshold'] = 'Limite para revisão no relatório';
$string['rule'] = 'Regra';
$string['ruleevidence'] = 'Observado: {$a->actual}; alvo/limite: {$a->limit}';
$string['rulelimit'] = 'Limite';
$string['rulemet'] = 'Cumprida';
$string['rulepending'] = 'Pendente';
$string['ruletype'] = 'Tipo de regra';
$string['ruletype:maxrate_lte'] = 'Velocidade máxima <= X';
$string['ruletype:percent_gte'] = 'Percentual assistido >= X';
$string['ruletype:reachedend'] = 'Chegou ao final do vídeo';
$string['ruletype:regularity_gte'] = 'Regularidade da reprodução >= X%';
$string['ruletype:replaycount_gte'] = 'Quantidade de revisões/replays >= X';
$string['ruletype:seekcount_lte'] = 'Quantidade de seeks <= X';
$string['ruletype:segment_watched'] = 'Segmento configurado assistido';
$string['ruletype:sessions_gte'] = 'Quantidade de sessões >= X';
$string['ruletype:sessions_lte'] = 'Quantidade de sessões <= X';
$string['ruletype:watchtime_gte'] = 'Tempo efetivo >= X segundos';
$string['saveindicator'] = 'Salvar indicador';
$string['scorecomposition'] = 'Composição do score';
$string['scoredistribution'] = 'Distribuição do score';
$string['scoreexplanation'] = 'O score é determinístico: cada indicador expõe métrica, limite, peso, pontos concedidos e estado da regra. Ele mede somente fatos configurados sobre o comportamento de reprodução.';
$string['scoreheader'] = 'Engagement Score e relatórios';
$string['scorenotyetcalculated'] = 'Ainda não existe Engagement Score em cache. O servidor fará o cálculo quando houver evidências de reprodução disponíveis.';
$string['seeks'] = 'Seeks';
$string['segmentcoverage'] = 'Cobertura mínima do segmento (%)';
$string['segmentdisplay'] = '{$a->start}s–{$a->end}s, cobertura mínima {$a->mincoverage}%';
$string['segmentend'] = 'Fim do segmento (segundos)';
$string['segmentstart'] = 'Início do segmento (segundos)';
$string['sessionend'] = 'Fim da sessão';
$string['sessions'] = 'Sessões';
$string['sessionstart'] = 'Início da sessão';
$string['state'] = 'Estado';
$string['status'] = 'Categoria de reprodução';
$string['statusheader'] = 'Categorias do comportamento de reprodução';
$string['taskreconcile'] = 'Reconciliar cache de analytics do Video Tracker Ultimate';
$string['teacherranking'] = 'Ranking exclusivo do professor';
$string['usegrade'] = 'Usar Engagement Score como nota';
$string['usegrade_help'] = 'Desabilitado por padrão. Quando habilitado, o Engagement Score determinístico de 0 a 100 é convertido para a nota máxima configurada e enviado ao Gradebook do Moodle.';
$string['usernotavailable'] = 'O usuário solicitado não está disponível no escopo atual da atividade/grupo.';
$string['videosource'] = 'Fonte do vídeo';
$string['videosourceheader'] = 'Fonte do vídeo';
$string['videotrackerultimate:addinstance'] = 'Adicionar uma atividade Video Tracker Ultimate';
$string['videotrackerultimate:export'] = 'Exportar relatórios do Video Tracker Ultimate';
$string['videotrackerultimate:manageindicators'] = 'Gerenciar indicadores do Engagement Score';
$string['videotrackerultimate:recalculate'] = 'Recalcular analytics de vídeo';
$string['videotrackerultimate:view'] = 'Visualizar Video Tracker Ultimate';
$string['videotrackerultimate:viewranking'] = 'Visualizar ranking exclusivo do professor';
$string['videotrackerultimate:viewreport'] = 'Visualizar relatórios do Video Tracker Ultimate';
$string['videotrackerultimatename'] = 'Nome da atividade';
$string['watchedmap'] = 'Mapa assistido';
$string['weightcomplete'] = 'Os pesos dos indicadores ativos totalizam exatamente 100 pontos.';
$string['weightwarning'] = 'Os indicadores ativos somam {$a}/100. O cálculo continua auditável e será normalizado para 0–100 enquanto a configuração não totalizar 100 pontos.';
