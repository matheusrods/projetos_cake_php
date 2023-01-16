USE [RHHealth]
GO

/****** Object:  Table [dbo].[fichas_assistenciais_log]    Script Date: 04/01/2023 20:35:35 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[fichas_assistenciais_log](
	[codigo] [int] IDENTITY(1,1) NOT NULL,
	[codigo_ficha_assistencial] [int] NOT NULL,
	[acao_sistema] [tinyint] NOT NULL,
	[codigo_pedido_exame] [int] NOT NULL,
	[codigo_medico] [int] NOT NULL,
	[pa_sistolica] [int] NULL,
	[pa_diastolica] [int] NULL,
	[pulso] [int] NULL,
	[circunferencia_abdominal] [decimal](5, 2) NULL,
	[circunferencia_quadril] [decimal](5, 2) NULL,
	[peso_kg] [int] NULL,
	[peso_gr] [int] NULL,
	[altura_mt] [int] NULL,
	[altura_cm] [int] NULL,
	[imc] [int] NULL,
	[parecer] [int] NULL,
	[parecer_altura] [int] NULL,
	[parecer_espaco_confinado] [int] NULL,
	[codigo_atestado] [int] NULL,
	[ativo] [int] NOT NULL,
	[codigo_empresa] [int] NOT NULL,
	[data_inclusao] [datetime] NOT NULL,
	[codigo_usuario_inclusao] [int] NOT NULL,
	[hora_inicio_atendimento] [time](7) NOT NULL,
	[hora_fim_atendimento] [time](7) NOT NULL,
    [data_alteracao] [datetime] NULL,
	[codigo_usuario_alteracao] [int] NULL,
    [data_alteracao] [datetime] NULL,
	[codigo_usuario_alteracao] [int] NULL,
PRIMARY KEY CLUSTERED 
(
	[codigo] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[codigo] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[codigo_pedido_exame] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[codigo_medico] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[pa_sistolica] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[pa_diastolica] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[pulso] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[circunferencia_abdominal] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[circunferencia_quadril] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[peso_kg] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[peso_gr] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[altura_mt] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[altura_cm] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[imc] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[parecer] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[parecer_altura] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[parecer_espaco_confinado] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[codigo_atestado] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[ativo] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[codigo_empresa] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[data_inclusao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[codigo_usuario_inclusao] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[hora_inicio_atendimento] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ADD SENSITIVITY CLASSIFICATION TO [dbo].[fichas_assistenciais_log].[hora_fim_atendimento] WITH (label = 'General', label_id = '684a0db2-d514-49d8-8c0c-df84a7b083eb', rank = Low);
GO

ALTER TABLE [dbo].[fichas_assistenciais_log] ADD  CONSTRAINT [df_fichas_assistenciais_log__data_inclusao]  DEFAULT (getdate()) FOR [data_inclusao]
GO

ALTER TABLE [dbo].[fichas_assistenciais_log]  WITH CHECK ADD  CONSTRAINT [fk_fichas_assistenciais_log__codigo_medico] FOREIGN KEY([codigo_medico])
REFERENCES [dbo].[medicos] ([codigo])
GO

ALTER TABLE [dbo].[fichas_assistenciais_log] CHECK CONSTRAINT [fk_fichas_assistenciais_log__codigo_medico]
GO

ALTER TABLE [dbo].[fichas_assistenciais_log]  WITH CHECK ADD  CONSTRAINT [fk_fichas_assistenciais_log__codigo_pedido_exame] FOREIGN KEY([codigo_pedido_exame])
REFERENCES [dbo].[pedidos_exames] ([codigo])
GO

ALTER TABLE [dbo].[fichas_assistenciais_log] CHECK CONSTRAINT [fk_fichas_assistenciais_log__codigo_pedido_exame]
GO

ALTER TABLE [dbo].[fichas_assistenciais_log]  WITH CHECK ADD  CONSTRAINT [fk_fichas_assistenciais_log__codigo_ficha_assistencial] FOREIGN KEY([codigo_ficha_assistencial])
REFERENCES [dbo].[fichas_assistenciais] ([codigo])
GO

ALTER TABLE [dbo].[fichas_assistenciais_log] CHECK CONSTRAINT [fk_fichas_assistenciais_log__codigo_ficha_assistencial]
GO
