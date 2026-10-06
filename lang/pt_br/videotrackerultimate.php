<?php
/**
 * Strings em Português do Brasil.
 *
 * @package mod_videotrackerultimate
 */

defined('MOODLE_INTERNAL') || die;

$string['pluginname'] = 'Video Tracker Ultimate';
$string['modulename'] = 'Video Tracker Ultimate';
$string['modulenameplural'] = 'Atividades Video Tracker Ultimate';
$string['modulename_help'] = 'Cria um Engagement Score explicável a partir de evidências de reprodução explicitamente configuradas.';
$string['pluginadministration'] = 'Administração do Video Tracker Ultimate';
$string['videotrackerultimatename'] = 'Nome da atividade';

$string['videotrackerultimate:addinstance'] = 'Adicionar uma atividade Video Tracker Ultimate';
$string['videotrackerultimate:view'] = 'Visualizar Video Tracker Ultimate';
$string['videotrackerultimate:viewreport'] = 'Visualizar relatórios do Video Tracker Ultimate';
$string['videotrackerultimate:manageindicators'] = 'Gerenciar indicadores do Engagement Score';
$string['videotrackerultimate:recalculate'] = 'Recalcular analytics de vídeo';
$string['videotrackerultimate:export'] = 'Exportar relatórios do Video Tracker Ultimate';
$string['videotrackerultimate:viewranking'] = 'Visualizar ranking exclusivo do professor';

$string['videosourceheader'] = 'Fonte do vídeo';
$string['videosource'] = 'Fonte do vídeo';
$string['scoreheader'] = 'Engagement Score e relatórios';
$string['usegrade'] = 'Usar Engagement Score como nota';
$string['usegrade_help'] = 'Desabilitado por padrão. Quando habilitado, o Engagement Score determinístico de 0 a 100 é convertido para a nota máxima configurada e enviado ao Gradebook do Moodle.';
$string['grademax'] = 'Nota máxima';
$string['reviewthreshold'] = 'Limite para revisão no relatório';
$string['rankingenabled'] = 'Habilitar ranking somente para professor';
$string['rankingenabled_help'] = 'Quando habilitado, usuários com a capability específica podem ordenar alunos pelo score. Nenhum ranking é exibido aos alunos.';
$string['statusheader'] = 'Categorias do comportamento de reprodução';
$string['excellentlabel'] = 'Rótulo da categoria superior';
$string['excellentmin'] = 'Mínimo da categoria superior';
$string['adequatelabel'] = 'Rótulo da segunda categoria';
$string['adequatemin'] = 'Mínimo da segunda categoria';
$string['attentionlabel'] = 'Rótulo da terceira categoria';
$string['attentionmin'] = 'Mínimo da terceira categoria';
$string['insufficientlabel'] = 'Rótulo da categoria inferior';
$string['errorgrademax'] = 'A nota máxima deve ser maior que zero e não pode ultrapassar 10000.';
$string['errorpercent'] = 'O valor deve ficar entre 0 e 100.';
$string['errorstatusorder'] = 'Os limites das categorias precisam estar ordenados do maior para o menor.';

$string['completionminscore'] = 'Exigir Engagement Score mínimo';
$string['completionminpercent'] = 'Exigir percentual mínimo assistido';
$string['completionindicators'] = 'Exigir indicadores marcados como obrigatórios para conclusão';
$string['completiondetail:score'] = 'Obter Engagement Score de pelo menos {$a}';
$string['completiondetail:percent'] = 'Assistir pelo menos {$a}% do vídeo';
$string['completiondetail:indicators'] = 'Cumprir todos os indicadores marcados como obrigatórios para conclusão';

$string['engagementscore'] = 'Engagement Score';
$string['scoreexplanation'] = 'O score é determinístico: cada indicador expõe métrica, limite, peso, pontos concedidos e estado da regra. Ele mede somente fatos configurados sobre o comportamento de reprodução.';
$string['normalizedscoreexplain'] = 'Os indicadores configurados concederam {$a->raw}/{$a->total} pontos brutos, normalizados de forma explícita para {$a->score}/100.';
$string['status'] = 'Categoria de reprodução';
$string['evidence'] = 'Evidência';
$string['points'] = 'Pontos';
$string['lastupdated'] = 'Última atualização dos analytics: {$a}';
$string['lastupdatedlabel'] = 'Última atualização';
$string['scorenotyetcalculated'] = 'Ainda não existe Engagement Score em cache. O servidor fará o cálculo quando houver evidências de reprodução disponíveis.';
$string['notcalculated'] = 'Não calculado';
$string['gradefeedbackorigin'] = 'Origem do recálculo do Video Tracker Ultimate: {$a}';

$string['indicators'] = 'Indicadores';
$string['indicator'] = 'Indicador';
$string['indicatorname'] = 'Nome';
$string['indicatorweight'] = 'Peso';
$string['indicatorenabled'] = 'Ativo';
$string['requiredcompletion'] = 'Obrigatório para conclusão';
$string['ruletype'] = 'Tipo de regra';
$string['rulelimit'] = 'Limite';
$string['rule'] = 'Regra';
$string['actual'] = 'Valor observado';
$string['state'] = 'Estado';
$string['saveindicator'] = 'Salvar indicador';
$string['addindicator'] = 'Adicionar indicador';
$string['editindicator'] = 'Editar indicador';
$string['indicatorsaved'] = 'Indicador salvo. Os scores existentes foram enfileirados para recálculo.';
$string['indicatordeleted'] = 'Indicador excluído. Os scores existentes foram enfileirados para recálculo.';
$string['errorweight'] = 'O peso deve ser maior que zero e não pode ultrapassar 100.';
$string['errortotalweight'] = 'Os indicadores ativos já usam {$a} pontos. O total ativo não pode ultrapassar 100.';
$string['weightwarning'] = 'Os indicadores ativos somam {$a}/100. O cálculo continua auditável e será normalizado para 0–100 enquanto a configuração não totalizar 100 pontos.';
$string['weightcomplete'] = 'Os pesos dos indicadores ativos totalizam exatamente 100 pontos.';
$string['segmentstart'] = 'Início do segmento (segundos)';
$string['segmentend'] = 'Fim do segmento (segundos)';
$string['segmentcoverage'] = 'Cobertura mínima do segmento (%)';
$string['segmentdisplay'] = '{$a->start}s–{$a->end}s, cobertura mínima {$a->mincoverage}%';
$string['backtoactivity'] = 'Voltar para a atividade';
$string['rulemet'] = 'Cumprida';
$string['rulepending'] = 'Pendente';
$string['ruleevidence'] = 'Observado: {$a->actual}; alvo/limite: {$a->limit}';

$string['ruletype:percent_gte'] = 'Percentual assistido >= X';
$string['ruletype:watchtime_gte'] = 'Tempo efetivo >= X segundos';
$string['ruletype:sessions_gte'] = 'Quantidade de sessões >= X';
$string['ruletype:sessions_lte'] = 'Quantidade de sessões <= X';
$string['ruletype:maxrate_lte'] = 'Velocidade máxima <= X';
$string['ruletype:reachedend'] = 'Chegou ao final do vídeo';
$string['ruletype:segment_watched'] = 'Segmento configurado assistido';
$string['ruletype:seekcount_lte'] = 'Quantidade de seeks <= X';
$string['ruletype:replaycount_gte'] = 'Quantidade de revisões/replays >= X';

$string['reports'] = 'Relatórios';
$string['classoverview'] = 'Visão geral da turma';
$string['individualreport'] = 'Evidências de reprodução de {$a}';
$string['backtooverview'] = 'Voltar para a visão geral';
$string['scorecomposition'] = 'Composição do score';
$string['recalculateanalytics'] = 'Recalcular analytics';
$string['recalculationqueued'] = '{$a} tarefa(s) de recálculo no servidor enfileirada(s).';
$string['calculationorigin'] = 'Origem do cálculo: {$a}';
$string['exportcsv'] = 'Exportar CSV';
$string['averagescore'] = 'Score médio';
$string['averagepercent'] = 'Percentual médio assistido';
$string['averagewatchtime'] = 'Tempo efetivo médio';
$string['belowthreshold'] = 'Alunos abaixo do limite de revisão';
$string['scoredistribution'] = 'Distribuição do score';
$string['frequentfailures'] = 'Indicadores não cumpridos com maior frequência';
$string['nofailures'] = 'Não há indicadores pendentes nos resultados atualmente em cache.';
$string['learners'] = 'Alunos';
$string['teacherranking'] = 'Ranking exclusivo do professor';
$string['rank'] = 'Posição';

$string['percentwatched'] = 'Percentual assistido';
$string['effectivetime'] = 'Tempo efetivo de reprodução';
$string['sessions'] = 'Sessões';
$string['seeks'] = 'Seeks';
$string['replays'] = 'Revisões/replays';
$string['maxrate'] = 'Velocidade máxima';
$string['maxposition'] = 'Maior posição alcançada';
$string['watchedmap'] = 'Mapa assistido';
$string['noduration'] = 'A duração do vídeo ainda não é conhecida.';
$string['sessionstart'] = 'Início da sessão';
$string['sessionend'] = 'Fim da sessão';
$string['usernotavailable'] = 'O usuário solicitado não está disponível no escopo atual da atividade/grupo.';

$string['eventscoreupdated'] = 'Engagement Score atualizado';
$string['taskreconcile'] = 'Reconciliar cache de analytics do Video Tracker Ultimate';

$string['privacy:path'] = 'Evidências do Video Tracker Ultimate';
$string['privacy:metadata:score'] = 'Armazena o score determinístico em cache e evidências normalizadas de reprodução.';
$string['privacy:metadata:score:userid'] = 'O aluno ao qual as evidências pertencem.';
$string['privacy:metadata:score:rawscore'] = 'Pontos brutos concedidos antes da normalização explícita.';
$string['privacy:metadata:score:totalweight'] = 'Soma dos pesos ativos usada no cálculo.';
$string['privacy:metadata:score:score'] = 'Engagement Score normalizado de 0 a 100.';
$string['privacy:metadata:score:statuscode'] = 'Código da categoria de comportamento de reprodução derivada dos limites configurados.';
$string['privacy:metadata:score:metricsjson'] = 'Métricas normalizadas obtidas pelas APIs públicas do Video Bridge.';
$string['privacy:metadata:score:breakdownjson'] = 'Composição completa e explicável dos indicadores.';
$string['privacy:metadata:score:calculatedby'] = 'Usuário que disparou explicitamente o cálculo, quando aplicável.';
$string['privacy:metadata:score:origin'] = 'Motivo no servidor que disparou o cálculo.';
$string['privacy:metadata:score:timecalculated'] = 'Quando as evidências em cache foram calculadas.';
$string['privacy:metadata:score:gradeupdated'] = 'Quando a nota do Gradebook foi atualizada pela última vez.';
$string['privacy:metadata:score:gradeorigin'] = 'Origem do cálculo responsável pela última atualização do Gradebook.';
$string['privacy:metadata:log'] = 'Armazena a trilha de auditoria dos recálculos de score.';
$string['privacy:metadata:log:userid'] = 'Aluno cujo score foi recalculado.';
$string['privacy:metadata:log:triggeredby'] = 'Usuário que disparou manualmente o recálculo, quando aplicável.';
$string['privacy:metadata:log:origin'] = 'Motivo do recálculo.';
$string['privacy:metadata:log:oldscore'] = 'Score normalizado anterior.';
$string['privacy:metadata:log:newscore'] = 'Novo score normalizado.';
$string['privacy:metadata:log:timecreated'] = 'Quando o recálculo ocorreu.';
